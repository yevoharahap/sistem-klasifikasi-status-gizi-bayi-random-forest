from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
import pickle
import time
import base64
from io import BytesIO
from sklearn.tree import export_text
import matplotlib
matplotlib.use("Agg")  # penting untuk server
import matplotlib.pyplot as plt
from sklearn.tree import plot_tree
from sklearn.tree import _tree
from sklearn.preprocessing import MinMaxScaler


# =====================================
# MAPPING KELAS STATUS GIZI (WAJIB ADA)
# =====================================
STATUS_GIZI_MAP = {
    0: "Underweight",
    1: "Stunting",
    2: "Wasting",
    3: "Obesitas"
}

app = Flask(__name__)

# Mapping gender
gender_map = {"L": 0, "P": 1}
gender_rev_map = {0: "L", 1: "P"}

# ======================================================
#                     TRAIN API
# ======================================================
@app.route("/train", methods=["POST"])
def train():
    try:
        data = request.get_json()
        df = pd.DataFrame(data["dataset"])

        # =========================
        # PREPROCESSING
        # =========================
        numeric_cols = ["usia_bulan", "berat_badan", "tinggi_badan", "imt"]
        missing_summary = df.isnull().sum().to_dict()

        for col in numeric_cols:
            df[col] = df[col].astype(str).str.replace(",", ".", regex=False)
            df[col] = pd.to_numeric(df[col], errors="coerce")

        # HAPUS SEMUA BARIS YANG MEMILIKI NILAI NULL
        df = df.dropna()

        df["usia_bulan"] = df["usia_bulan"].round(0).astype(int)

        df = df.sort_values(
            by=["jenis_kelamin", "usia_bulan", "berat_badan", "tinggi_badan", "imt"]
        ).reset_index(drop=True)

        df_original = df.copy()   # SIMPAN DATA ASLI

        df = df.reset_index(drop=True)

        # =========================
        # NORMALISASI (MIN-MAX SCALER)
        # =========================
        scaler = MinMaxScaler()
        df[numeric_cols] = scaler.fit_transform(df[numeric_cols])

        # =========================
        # ENCODE GENDER
        # =========================
        le_gender = LabelEncoder()
        df["jenis_kelamin_enc"] = le_gender.fit_transform(df["jenis_kelamin"])

        # ================================
        # PERBAIKI CASING STATUS GIZI
        # ================================
        df["status_gizi"] = df["status_gizi"].str.strip().str.title()

        # ================================
        # LabelEncoder le_status dengan urutan kelas konsisten
        # ================================
        le_status = LabelEncoder()
        le_status.fit(["Underweight", "Stunting", "Wasting", "Obesitas"])
        df["status_gizi_enc"] = le_status.transform(df["status_gizi"])

        # =========================
        # SPLIT DATA
        # =========================
        X = df[["jenis_kelamin_enc", "usia_bulan", "berat_badan", "tinggi_badan", "imt"]]
        y = df["status_gizi_enc"]

        test_size = float(data.get("test_size", 0.2))
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=test_size, random_state=42, stratify=y
        )

        # =========================
        # RANDOM FOREST PARAMETER
        # =========================
        n_estimators = int(data.get("n_estimators", 200))
        max_depth = data.get("max_depth", None)
        if max_depth:
            max_depth = int(max_depth)

        min_samples_split = int(data.get("min_samples_split", 2))

        # Tambahkan class_weight="balanced"
        model = RandomForestClassifier(
            n_estimators=n_estimators,
            max_depth=max_depth,
            min_samples_split=min_samples_split,
            random_state=42,
        )

        # =========================
        # TRAINING TIME
        # =========================
        start_time = time.time()
        model.fit(X_train, y_train)
        end_time = time.time()
        training_time = end_time - start_time

        # =========================
        # PREDIKSI
        # =========================
        y_pred = model.predict(X_test)
        acc = accuracy_score(y_test, y_pred)

        class_labels = list(le_status.classes_)
        cm = confusion_matrix(y_test, y_pred, labels=range(len(class_labels))).tolist()

        # =========================
        # CLASSIFICATION REPORT
        # =========================
        class_report_dict = classification_report(
            y_test, y_pred, target_names=class_labels, output_dict=True
        )

        report_list = []
        for cls in class_labels:
            report_list.append({
                "kelas": cls,
                "precision": float(class_report_dict[cls]["precision"]),
                "recall": float(class_report_dict[cls]["recall"]),
                "f1": float(class_report_dict[cls]["f1-score"]),
                "support": int(class_report_dict[cls]["support"]),
            })

        # Macro & Weighted Avg
        for avg_type in ["macro avg", "weighted avg"]:
            report_list.append({
                "kelas": avg_type.title(),
                "precision": float(class_report_dict[avg_type]["precision"]),
                "recall": float(class_report_dict[avg_type]["recall"]),
                "f1": float(class_report_dict[avg_type]["f1-score"]),
                "support": int(class_report_dict[avg_type]["support"]),
            })

        # Accuracy
        report_list.append({
            "kelas": "Accuracy",
            "precision": float(acc),
            "recall": float(acc),
            "f1": float(acc),
            "support": int(sum([class_report_dict[c]["support"] for c in class_labels])),
        })

        # =========================
        # PROBABILITAS
        # =========================
        y_proba = model.predict_proba(X_test)

        # =========================
        # SERIALIZE MODEL KE BASE64 (SIMPAN SCALER JUGA)
        # =========================
        model_bytes = BytesIO()
        pickle.dump({
            "model": model,
            "le_status": le_status,
            "scaler": scaler  # <-- tambahkan ini agar /predict bisa transform input
        }, model_bytes)
        model_bytes.seek(0)
        model_base64 = base64.b64encode(model_bytes.read()).decode("utf-8")

        # =========================
        # TRAIN & TEST DATA
        # =========================
        train_df = X_train.copy()
        train_df["jenis_kelamin"] = train_df["jenis_kelamin_enc"].map(gender_rev_map)
        train_df["status_gizi"] = le_status.inverse_transform(y_train)
        train_df["usia_bulan"] = X_train["usia_bulan"].astype(int)
        train_data = train_df.to_dict(orient="records")

        test_df = X_test.copy()
        test_df["jenis_kelamin"] = test_df["jenis_kelamin_enc"].map(gender_rev_map)
        test_df["status_gizi"] = le_status.inverse_transform(y_test)
        test_df["usia_bulan"] = X_test["usia_bulan"].astype(int)
        test_data = test_df.to_dict(orient="records")

        # =========================
        # BATCH RESULTS
        # =========================
        result_df = X_test.copy()
        result_df["jenis_kelamin"] = result_df["jenis_kelamin_enc"].map(gender_rev_map)
        result_df["usia_bulan"] = df_original.loc[X_test.index, "usia_bulan"]
        result_df["berat_badan"] = df_original.loc[X_test.index, "berat_badan"]
        result_df["tinggi_badan"] = df_original.loc[X_test.index, "tinggi_badan"]
        result_df["imt"] = df_original.loc[X_test.index, "imt"]
        result_df["status_gizi_aktual"] = le_status.inverse_transform(y_test)
        result_df["prediksi"] = le_status.inverse_transform(y_pred)

        proba_list = []
        for i in range(len(result_df)):
            probs = {label: round(float(y_proba[i][idx]) * 100, 2)
                     for idx, label in enumerate(class_labels)}
            proba_list.append(probs)

        result_df["probabilitas"] = proba_list
        batch_results = result_df.to_dict(orient="records")

        # =========================
        # RESPONSE JSON
        # =========================
        return jsonify({
            "accuracy": float(acc),
            "confusion_matrix": cm,
            "labels": class_labels,
            "classification_report": report_list,
            "batch_results": batch_results,
            "train_data": train_data,
            "test_data": test_data,
            "training_time": round(training_time, 4),
            "preprocessing": {
                "total_data": len(df),
                "numeric_columns": numeric_cols,
                "missing": missing_summary
            },
            "model_base64": model_base64  # <-- Laravel akan simpan ini ke file & DB
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 400


# ======================================================
#               TREE ANALYSIS API (FINAL)
# ======================================================
@app.route('/tree_analysis', methods=['POST'])
def tree_analysis():
    try:
        data = request.get_json()

        if 'model_base64' not in data:
            return jsonify({'error': 'Model tidak dikirim'}), 400

        tree_index = int(data.get('tree_index', 0))

        # =============================
        # LOAD MODEL
        # =============================
        model_obj = pickle.loads(base64.b64decode(data['model_base64']))
        model = model_obj['model']   # ⬅️ INI PENTING

        if not isinstance(model, RandomForestClassifier):
            return jsonify({'error': 'Model bukan Random Forest'}), 400

        if tree_index >= len(model.estimators_):
            return jsonify({'error': 'Index pohon di luar jangkauan'}), 400

        estimator = model.estimators_[tree_index]
        tree = estimator.tree_

        feature_names = model.feature_names_in_
        class_names = model.classes_

        sample_data = data.get("sample_data", None)
        decision_path_result = []

        # =============================
        # INFO POHON
        # =============================
        tree_info = {
            "tree_index": tree_index,
            "depth": int(tree.max_depth),
            "node_count": int(tree.node_count)
        }

        # =============================
        # FEATURE IMPORTANCE (GLOBAL RF)
        # =============================
        importances = model.feature_importances_

        feature_importance = []
        for name, score in zip(feature_names, importances):
            feature_importance.append({
                "fitur": name,
                "importance": round(float(score), 6)
            })

        # Optional: urutkan dari terbesar
        feature_importance = sorted(
            feature_importance,
            key=lambda x: x["importance"],
            reverse=True
        )


        # =============================
        # EKSTRAK RULE LENGKAP (ITERATIVE)
        # =============================
        rules = []
        stack = [(0, [])]  # node_id, conditions

        while stack:
            node_id, conditions = stack.pop()

            # LEAF
            if tree.children_left[node_id] == _tree.TREE_LEAF:
                value = tree.value[node_id][0]

                # Ambil kelas numerik hasil prediksi
                pred_class_num = int(class_names[np.argmax(value)])

                # Konversi ke label status gizi
                pred_class_label = STATUS_GIZI_MAP.get(
                    pred_class_num,
                    f"Kelas {pred_class_num}"
                )

                rules.append({
                    "aturan": " DAN ".join(conditions),
                    "prediksi": pred_class_label,
                    "prediksi_kode": pred_class_num,  # opsional (berguna untuk analisis)
                    "gini": round(float(tree.impurity[node_id]), 4),
                    "samples": int(tree.n_node_samples[node_id])
                })
                continue


            # INTERNAL NODE
            feature = feature_names[tree.feature[node_id]]
            threshold = round(float(tree.threshold[node_id]), 3)

            left = f"{feature} ≤ {threshold}"
            right = f"{feature} > {threshold}"

            stack.append((tree.children_right[node_id], conditions + [right]))
            stack.append((tree.children_left[node_id], conditions + [left]))

        # =============================
        # VISUALISASI POHON
        # =============================
        fig = plt.figure(figsize=(20, 10))
        from sklearn.tree import plot_tree
        plot_tree(
            estimator,
            feature_names=feature_names,
            class_names=[str(c) for c in class_names],
            filled=True,
            rounded=True,
            fontsize=8
        )

        buf = BytesIO()
        plt.savefig(buf, format="png", bbox_inches="tight")
        plt.close(fig)
        buf.seek(0)

        image_base64 = base64.b64encode(buf.read()).decode("utf-8")

        # =============================
        # RESPONSE
        # =============================
        return jsonify({
            "tree_info": tree_info,
            "feature_importance": feature_importance,
            "rules": rules,
            "image_base64": image_base64
        })


    except Exception as e:
        return jsonify({'error': str(e)}), 400


# ======================================================
#                   PREDICT API (FIXED)
# ======================================================
@app.route("/predict", methods=["POST"])
def predict():
    try:
        start_time = time.time()

        data = request.get_json()
        model_base64 = data.get("model_base64")
        dataset = data.get("data", [])

        if not model_base64:
            return jsonify({"error": "Model tidak diterima"}), 400

        model_bytes = BytesIO(base64.b64decode(model_base64))
        obj = pickle.load(model_bytes)

        model = obj["model"]
        le_status = obj["le_status"]
        scaler = obj.get("scaler")

        results = []

        for row in dataset:

            X = np.array([[
                gender_map[row["jenis_kelamin"]],
                float(row["usia_bulan"]),
                float(row["berat_badan"]),
                float(row["tinggi_badan"]),
                float(row["imt"])
            ]])

            if scaler:
                X[:,1:] = scaler.transform(X[:,1:])

            pred = model.predict(X)[0]
            label = le_status.inverse_transform([pred])[0]

            proba = model.predict_proba(X)[0]

            proba_dict = {
                le_status.classes_[i]: round(float(proba[i])*100,2)
                for i in range(len(proba))
            }

            results.append({
                "prediction": label,
                "probability": proba_dict
            })

        end_time = time.time()

        return jsonify({
            "predictions": results,
            "execution_time": round(end_time - start_time,4)
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 400

# ======================================================
#                     RUN SERVER
# ======================================================
if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)

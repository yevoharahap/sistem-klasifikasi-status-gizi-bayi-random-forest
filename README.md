# 🌴 Palm Oil Variety Classification System (Laravel + Flask + KNN)

A web-based machine learning system for classifying oil palm varieties into **Recommended (R)** and **Not Recommended (TD)** categories using the **K-Nearest Neighbors (KNN)** algorithm.

This system integrates a **Laravel web application** with a **Python Flask API** to perform machine learning processes including data preprocessing, model training, evaluation, and classification of new oil palm plantation data.

The application is designed to assist plantation managers, researchers, and agricultural practitioners in identifying oil palm varieties that have the potential to provide better production performance based on production and plantation waste indicators.

---

# 🧠 System Architecture

This project uses a microservice architecture where the web application and machine learning service run separately:

```text
User
   ↓
Laravel Web Application
   ↓
HTTP Request (JSON)
   ↓
Flask API
   ↓
K-Nearest Neighbors (KNN) Model
   ↓
Classification Result
   ↓
Laravel Web Application
   ↓
User
```

### Laravel Handles

* User interface (UI)
* Dataset management
* Model management
* Training configuration
* Classification requests
* Result visualization
* Report generation
* Data storage

### Flask Handles

* Data preprocessing
* Feature normalization (MinMaxScaler)
* Label encoding
* KNN model training
* Classification process
* Probability calculation
* Model serialization
* Performance evaluation

Laravel communicates with Flask using REST API and JSON responses.

---

# 🚀 Main Features

## 📊 Dataset Management

* Add oil palm dataset manually
* Import dataset from Excel (.xlsx/.xls)
* Edit dataset
* Delete dataset
* Store dataset in MySQL database

Dataset attributes include:

* Produksi TBS
* Rendemen CPO
* Produksi Kernel
* Limbah Tandan Kosong
* Limbah Cangkang
* Limbah Serat
* Varietas
* Label

---

## 🤖 Machine Learning Training

The system provides a complete machine learning training workflow:

### Data Preprocessing

* Data validation
* Missing value handling
* Numeric conversion
* Min-Max Normalization

### Dataset Splitting

* Custom Train-Test Split
* 50% – 95% training ratio support

### KNN Training

* Adjustable K value
* Automatic model training
* Model performance calculation

### Model Storage

* Save trained models
* Reuse trained models for future classifications

---

## 🌴 Oil Palm Variety Classification

The system classifies oil palm varieties into:

### R (Recommended)

Varieties that meet production and plantation waste criteria and are considered suitable for recommendation.

### TD (Not Recommended)

Varieties that do not meet recommendation criteria based on trained model patterns.

Classification can be performed using:

### Manual Input

Users can directly enter plantation data through the application.

### Excel / CSV Upload

Users can upload multiple records for batch classification.

---

## 📈 Model Evaluation

The system automatically evaluates model performance using:

* Accuracy Score
* Precision
* Recall
* F1-Score
* Confusion Matrix
* Classification Report

Performance metrics help users determine model reliability before deployment.

---

## 📋 Classification Result Management

* Display prediction results
* Display classification probabilities
* Save classification history
* Generate PDF reports
* View previous classification records

---

## 📑 PDF Reporting

The system can generate classification reports containing:

* Classification information
* Model information
* Input dataset
* Prediction results
* Recommendation statistics
* Classification probabilities

Reports can be downloaded and printed directly.

---

# 🧱 Technologies Used

| Component                  | Technology                |
| -------------------------- | ------------------------- |
| Web Framework              | Laravel 12                |
| Machine Learning API       | Python Flask              |
| Machine Learning Algorithm | K-Nearest Neighbors (KNN) |
| Data Processing            | Pandas                    |
| Machine Learning Library   | Scikit-Learn              |
| Database                   | MySQL                     |
| Frontend                   | Blade Template            |
| Styling                    | Bootstrap 5               |
| Visualization              | Chart.js                  |
| API Communication          | REST API (JSON)           |
| Runtime Environment        | PHP 8+, Python 3.10+      |

---

# 📁 Project Structure

```text
classification_knn/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
│
├── routes/
│   └── web.php
│
├── resources/
│   └── views/
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── public/
│
├── storage/
│   └── app/public/model/
│
├── python/
│   ├── api_knn.py
│   └── requirements.txt
│
├── .env.example
│
└── README.md
```

---

# ⚙️ How to Run This Project

```text
# ==========================================
# Palm Oil Variety Classification System
# Laravel + Flask Setup Guide
# ==========================================

# 1. Clone Repository
git clone https://github.com/yevoharahap/sistem-klasifikasi-varietas-kelapa-sawit.git

cd sistem-klasifikasi-varietas-kelapa-sawit


# ==========================================
# 2. LARAVEL SETUP
# ==========================================

# Install PHP Dependencies
composer install

# Create Environment File
copy .env.example .env

# Generate Application Key
php artisan key:generate


# Create MySQL Database
# Example:
# database name = klasifikasi_sawit


# Configure .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klasifikasi_sawit
DB_USERNAME=root
DB_PASSWORD=


# Run Migration
php artisan migrate


# Create Storage Link
php artisan storage:link


# Install Frontend Dependencies
npm install


# Build Assets
npm run build


# Run Laravel Server
php artisan serve


# ==========================================
# 3. PYTHON FLASK SETUP
# ==========================================

# Open New Terminal

cd python

# Create Virtual Environment
python -m venv venv

# Activate Virtual Environment (Windows)
venv\Scripts\activate

# Linux / Mac
# source venv/bin/activate


# Install Python Dependencies
pip install -r requirements.txt


# Run Flask API
python api_knn.py


# ==========================================
# ACCESS APPLICATION
# ==========================================

Laravel Application
http://127.0.0.1:8000

Flask API
http://127.0.0.1:5000
```

---

# ⚠️ Important Notes

1. Laravel Server and Flask API must run simultaneously.
2. Classification features will not work if Flask API is not running.
3. Trained models are stored in the storage directory and must not be deleted.
4. Uploaded datasets must follow the required column format.
5. Ensure Python dependencies are installed before running Flask.

---

# 📄 Required Dataset Format

Dataset files (.xlsx, .xls, .csv) must contain the following columns:

```text
produksi_tbs
rendeman_cpo
produksi_kernel
limbah_tandan_kosong
limbah_cangkang
limbah_serat
varietas
label
```

Example:

| produksi_tbs | rendeman_cpo | produksi_kernel | limbah_tandan_kosong | limbah_cangkang | limbah_serat | varietas       | label |
| ------------ | ------------ | --------------- | -------------------- | --------------- | ------------ | -------------- | ----- |
| 24.5         | 22.3         | 5.1             | 5.6                  | 1.2             | 2.5          | DxP Simalungun | R     |
| 18.7         | 19.5         | 3.8             | 4.8                  | 0.9             | 1.8          | Marihat        | TD    |

---

# 👨‍💻 Developer

**Yevo Harahap**

Final Project:
**Classification of Oil Palm Varieties Using K-Nearest Neighbors (KNN) Method with Weight Voting**

PT. Seumadam, Aceh Tamiang, Aceh

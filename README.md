## 🧒 Baby Nutritional Status Classification System (Laravel + Flask)

A web-based system for classifying the nutritional status of infants using Machine Learning with the Random Forest algorithm.
This system integrates a Laravel web application with a Python Flask API to perform machine learning classification based on infant growth and nutritional data.
The application helps healthcare workers, researchers, or parents analyze infant nutritional conditions and classify them into appropriate nutritional status categories.

## 🧠 System Architecture
This project uses a microservice architecture where the web application and machine learning service run separately :

User → Laravel Web App → HTTP Request → Flask API → Random Forest Model → Classification Result → Laravel → User

**Explanation**

**Laravel handles:**
- user authentication
- data input and management
- dataset visualization
- sending data to the ML API
- displaying classification results

**Flask handles:**
- data preprocessing
- model training
- Random Forest classification
- model evaluation
- prediction results

Laravel communicates with Flask via HTTP API (JSON Response).

## 🚀 Main Features

**Dataset Management**
- Input infant nutritional data
- Edit and delete dataset
- Store dataset in MySQL database

**Machine Learning Processing**
- Data preprocessing
- Feature selection
- Dataset splitting (train/test)
- Random Forest model training
- Automatic classification process

**Classification**
The system classifies infant nutritional status such as:
- Normal Nutrition
- Under Nutrition
- Over Nutrition

based on features like:
- age
- weight
- height
- other nutritional indicators

**Model Evaluation**
- Accuracy Score
- Confusion Matrix
- Classification Report
- Performance metrics visualization

**Visualization**
- Interactive charts
- Dataset distribution
- Model performance visualization

## 🧱 Technologies Used

| Component | Technology |
|--------|--------|
| Web Framework | Laravel 12 (PHP) |
| Machine Learning API | Python Flask |
| Database | MySQL |
| Visualization | Chart.js  / JavaScript |
| Styling | Bootstrap / Tailwind |
| ML Library | scikit-learn |
| Communication | REST API (JSON HTTP) |
| Runtime Environment | PHP 8+, Python 3.10+ |

## 📁 Project Structure
```text
project-root/
│
├── app/                    # Laravel Controllers & Business Logic
├── routes/                 # Web Routes
├── resources/
│   └── views/              # Blade Templates (UI Pages)
├── database/               # Migrations & Seeders
├── public/                 # CSS, JS, Images, Assets
│
├── python/                 # Flask Machine Learning Service
│   ├── api.py              # Random Forest API Endpoint
│   └── requirements.txt    # Python Dependencies
│
└── .env.example            # Environment Configuration Template
```

## ⚙️ How to Run This Project
```text
# ==========================================
# Baby Nutritional Status Classification
# Laravel + Flask Setup Guide
# ==========================================

# 1. Clone repository
git clone https://github.com/yevoharahap/sistem-klasifikasi-status-gizi-bayi-random-forest.git

cd sistem-klasifikasi-status-gizi-bayi-random-forest


# ================================
# 2. LARAVEL SETUP (WEB SYSTEM)
# ================================

# install PHP dependencies
composer install

# create environment file
cp .env.example .env

# generate application key
php artisan key:generate

# IMPORTANT:
# create a MySQL database first (example: gizi_bayi)

# then edit .env and configure:
DB_DATABASE=gizi_bayi
DB_USERNAME=root
DB_PASSWORD=

# migrate database
php artisan migrate

# install frontend dependencies
npm install

# build frontend assets
npm run build

# run laravel server
php artisan serve


# ================================
# 3. PYTHON FLASK SETUP (ML API)
# ================================

# open new terminal
cd python

# create virtual environment
python -m venv venv

# activate venv (Windows)
venv\Scripts\activate

# Linux / Mac alternative
# source venv/bin/activate

# install python libraries
pip install -r requirements.txt

# run Flask API
python api.py


# ================================
# ACCESS APPLICATION
# ================================

# Laravel Web Application
http://127.0.0.1:8000

# Flask Machine Learning API
http://127.0.0.1:5000


# IMPORTANT
Both servers must be running simultaneously.

If the classification feature does not work,
make sure the Flask API server is active.
```

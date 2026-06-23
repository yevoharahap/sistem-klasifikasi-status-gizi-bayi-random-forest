# 🧒 Baby Nutritional Status Classification System (Laravel + Flask)

A web-based system for classifying the nutritional status of infants using Machine Learning with the Random Forest algorithm.

This system integrates a Laravel web application with a Python Flask API to perform machine learning classification based on infant growth and nutritional data.

The application helps healthcare workers, researchers, or parents analyze infant nutritional conditions and classify them into appropriate nutritional status categories.

---

# 🧠 System Architecture

This project uses a microservice architecture where the web application and machine learning service run separately:

```text
User
    ↓
Laravel Web App
    ↓
HTTP Request
    ↓
Flask API
    ↓
Random Forest Model
    ↓
Classification Result
    ↓
Laravel
    ↓
User
```

## Explanation

### Laravel Handles

* User authentication
* Data input and management
* Dataset visualization
* Sending data to the ML API
* Displaying classification results

### Flask Handles

* Data preprocessing
* Model training
* Random Forest classification
* Model evaluation
* Prediction results

Laravel communicates with Flask via HTTP API using JSON responses.

---

# 🚀 Main Features

## Dataset Management

* Input infant nutritional data
* Edit and delete dataset
* Store dataset in MySQL database

## Machine Learning Processing

* Data preprocessing
* Feature selection
* Dataset splitting (train/test)
* Random Forest model training
* Automatic classification process

## Classification

The system classifies infant nutritional status into categories such as:

* Normal Nutrition
* Under Nutrition
* Over Nutrition

Based on features including:

* Age
* Weight
* Height
* Other nutritional indicators

## Model Evaluation

* Accuracy Score
* Confusion Matrix
* Classification Report
* Performance metrics visualization

## Visualization

* Interactive charts
* Dataset distribution
* Model performance visualization

---

# 🧱 Technologies Used

| Component            | Technology            |
| -------------------- | --------------------- |
| Web Framework        | Laravel 12 (PHP)      |
| Machine Learning API | Python Flask          |
| Database             | MySQL                 |
| Visualization        | Chart.js / JavaScript |
| Styling              | Bootstrap / Tailwind  |
| ML Library           | Scikit-learn          |
| Communication        | REST API (JSON HTTP)  |
| Runtime Environment  | PHP 8+, Python 3.10+  |

---

# 📁 Project Structure

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

---

# ⚙️ How to Run This Project

```bash
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

# Install PHP dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# IMPORTANT:
# Create a MySQL database first
# Example: gizi_bayi

# Configure .env
DB_DATABASE=gizi_bayi
DB_USERNAME=root
DB_PASSWORD=

# Run migration
php artisan migrate

# Install frontend dependencies
npm install

# Build frontend assets
npm run build

# Run Laravel server
php artisan serve


# ================================
# 3. PYTHON FLASK SETUP (ML API)
# ================================

# Open new terminal
cd python

# Create virtual environment
python -m venv venv

# Activate virtual environment (Windows)
venv\Scripts\activate

# Linux / Mac alternative
# source venv/bin/activate

# Install Python libraries
pip install -r requirements.txt

# Run Flask API
python api.py


# ================================
# ACCESS APPLICATION
# ================================

# Laravel Web Application
http://127.0.0.1:8000

# Flask Machine Learning API
http://127.0.0.1:5000
```

---

# ⚠️ Important Notes

* Both Laravel and Flask servers must be running simultaneously.
* If the classification feature does not work, make sure the Flask API server is active.
* Ensure all required dependencies have been installed successfully before running the application.

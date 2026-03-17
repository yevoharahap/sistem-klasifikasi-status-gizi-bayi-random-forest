<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataAnakController;
use App\Http\Controllers\KlasifikasiController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\HasilPrediksiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| AUTH (Login & Register)
|--------------------------------------------------------------------------
*/

// FORM LOGIN
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
// PROSES LOGIN
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// FORM REGISTER
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
// PROSES REGISTER
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Data Anak (CRUD)
    |----------------------------------------------------------------------
    */
    Route::get('/data-anak', [DataAnakController::class, 'index'])->name('data-anak.index');
    Route::get('/data-anak/create', [DataAnakController::class, 'create'])->name('data-anak.create');
    Route::post('/data-anak', [DataAnakController::class, 'store'])->name('data-anak.store');
    Route::get('/data-anak/{id}/edit', [DataAnakController::class, 'edit'])->name('data-anak.edit');
    Route::put('/data-anak/{id}', [DataAnakController::class, 'update'])->name('data-anak.update');
    Route::delete('/data-anak/{id}', [DataAnakController::class, 'destroy'])->name('data-anak.destroy');
    Route::delete('/data-anak', [DataAnakController::class, 'destroyAll'])->name('data-anak.destroyAll');
    Route::post('/data-anak/import', [DataAnakController::class, 'import'])->name('data-anak.import');

    /*
    |----------------------------------------------------------------------
    | Klasifikasi (Training Model)
    |----------------------------------------------------------------------
    */
    Route::get('/klasifikasi', [KlasifikasiController::class, 'index'])->name('klasifikasi.index');
    Route::post('/klasifikasi/train', [KlasifikasiController::class, 'train'])->name('klasifikasi.train');
    Route::post('/klasifikasi/save-model', [KlasifikasiController::class, 'saveModel'])->name('klasifikasi.save_model');
    Route::post('/klasifikasi/clear-results', [KlasifikasiController::class, 'clearResults'])
        ->name('klasifikasi.clear_results');

    Route::post('/klasifikasi/tree-analysis',
    [KlasifikasiController::class, 'treeAnalysis']
            )->name('klasifikasi.tree_analysis');

    /*
    |----------------------------------------------------------------------
    | Prediksi
    |----------------------------------------------------------------------
    */
    Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/predict', [PrediksiController::class, 'predict'])->name('prediksi.predict');
    Route::post('/prediksi/save', [PrediksiController::class, 'save'])->name('prediksi.save');
    Route::post('/prediksi/clear', [PrediksiController::class, 'clear'])->name('prediksi.clear');
    Route::get('/prediksi/hasil', [PrediksiController::class, 'hasil'])->name('hasil.index');

    /*
    |----------------------------------------------------------------------
    | Model ML
    |----------------------------------------------------------------------
    */
    Route::get('/models', [ModelController::class, 'index'])->name('models.index');
    Route::post('/models/store', [ModelController::class, 'store'])->name('models.store');
    Route::get('/models/{id}', [ModelController::class, 'show'])->name('models.show');
    Route::delete('/models/{id}', [ModelController::class, 'destroy'])->name('models.destroy');

    /*
    |----------------------------------------------------------------------
    | Hasil Prediksi (Listing & PDF)
    |----------------------------------------------------------------------
    */
    Route::get('/hasil', [HasilPrediksiController::class, 'index'])->name('hasil.all');
    Route::get('/hasil/delete/{id}', [HasilPrediksiController::class, 'delete'])->name('hasil.delete');
    Route::get('/hasil/pdf/{id}', [HasilPrediksiController::class, 'pdf'])->name('hasil.pdf');
});


// Group middleware auth agar hanya user login yang bisa akses
Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/users', [UserController::class, 'store'])->name('user.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});

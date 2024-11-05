<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FilvineWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login'); 
});

Route::get('/check', function () {
    return 'Hello World';
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'mainDashboard'])
    ->name('dashboard');
    Route::get('/change_password', [AdminController::class, 'changePassword'])
    ->name('change_password');
    Route::post('/store_new_password', [AdminController::class, 'storeNewPassword'])
    ->name('store.new.password');
    Route::get('/profile', [AdminController::class, 'editProfile'])
    ->name('edit.profile');
    Route::post('/store_update_profile', [AdminController::class, 'storeUpdateProfile'])
    ->name('store.update.profile');

    //Companies
    Route::get('/companies', [CompanyController::class, 'index'])
    ->name('companies');
    Route::get('/add_company', [CompanyController::class, 'addCompany'])
    ->name('add.company');
    Route::post('/store_company', [CompanyController::class, 'store'])
    ->name('store.company');

});

require __DIR__.'/auth.php';

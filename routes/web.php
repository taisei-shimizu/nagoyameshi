<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ReviewManagementController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\CategoryManagementController;
use App\Http\Controllers\ShopManagementController;
use App\Http\Controllers\CompanyManagementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SalesController;

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

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('guest');;
Route::get('/company', [CompanyManagementController::class, 'show'])->name('company');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('shops', ShopController::class);
    Route::controller(UserController::class)->group(function () {
        Route::get('users/mypage', 'mypage')->name('mypage');
        Route::get('users/mypage/edit', 'edit')->name('mypage.edit');
        Route::put('users/mypage', 'update')->name('mypage.update');
        Route::get('users/mypage/favorites', [UserController::class, 'favorites'])->name('mypage.favorites');
        Route::get('users/mypage/reservations', [UserController::class, 'reservations'])->name('mypage.reservations');
        Route::delete('users/delete', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

Route::middleware(['auth', 'verified', 'is_paid_member'])->group(function () {
    Route::post('/shops/{shop}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/shops/{shop}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('/shops/{shop}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/shops/{shop}/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::get('payment/edit', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::post('payment/update', [PaymentController::class, 'update'])->name('payment.update');
    Route::post('payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

Route::middleware(['auth', 'verified', 'is_free_member'])->group(function () {
    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');
    // Route::post('payment/success', [PaymentController::class, 'success'])->name('payment.success');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/export', [UserManagementController::class, 'export'])->name('users.export');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('reviews', [ReviewManagementController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/toggle-publish', [ReviewManagementController::class, 'togglePublish'])->name('reviews.togglePublish');
    Route::resource('admins', AdminManagementController::class)->except(['show']);
    Route::resource('categories', CategoryManagementController::class)->except(['show']);
    Route::resource('shops', ShopManagementController::class)->except(['show']);
    Route::get('shops/export', [ShopManagementController::class, 'export'])->name('shops.export');
    Route::post('shops/import', [ShopManagementController::class, 'import'])->name('shops.import');
    Route::get('shops/template', [ShopManagementController::class, 'downloadTemplate'])->name('shops.template');
    Route::get('company/edit', [CompanyManagementController::class, 'edit'])->name('company.edit');
    Route::patch('company/update', [CompanyManagementController::class, 'update'])->name('company.update');
    Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
});

require __DIR__.'/auth.php';

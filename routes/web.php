<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ==================== PUBLIC ROUTES ====================
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');
// ==================== AUTH ROUTES ====================
Route::middleware('guest')->group(function () {
    // Đăng ký
    Route::get('/dangky', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/dangky', [AuthController::class, 'register'])->name('register.post');
    
    // Đăng nhập
    Route::get('/dangnhap', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dangnhap', [AuthController::class, 'login'])->name('login.post');
    
    // Quên mật khẩu
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'updatePasswordSimple'])->name('password.update.simple');

    // Social login
    // Social login - KHÔNG CÓ middleware guest
    Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider'])
        ->name('social.login')
        ->where('provider', 'google|facebook');

    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
});

// Đăng xuất
Route::post('/dangxuat', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // --- QUẢN LÝ SẢN PHẨM ---
    Route::get('/products', [AdminController::class, 'products'])->name('products'); // Danh sách
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create'); // Form thêm
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store'); // Xử lý thêm
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit'); // Form sửa
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update'); // Xử lý sửa
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.destroy'); // Xóa
    
    Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/promotions', [AdminController::class, 'promotions'])->name('promotions');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

// ==================== STAFF ROUTES ====================
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('dashboard');
    Route::get('/orders', [StaffController::class, 'orders'])->name('orders');
    Route::get('/products', [StaffController::class, 'products'])->name('products');
    Route::get('/customers', [StaffController::class, 'customers'])->name('customers');
    Route::get('/support', [StaffController::class, 'support'])->name('support');
    Route::get('/reports', [StaffController::class, 'reports'])->name('reports');
});

// ==================== CUSTOMER ROUTES ====================
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/wishlist', [CustomerController::class, 'wishlist'])->name('wishlist');
    Route::get('/reviews', [CustomerController::class, 'reviews'])->name('reviews');
});
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
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

    Route::get('/accounts/create', [AdminController::class, 'createAccount'])->name('accounts.create');
    Route::post('/accounts', [AdminController::class, 'storeAccount'])->name('accounts.store');
    // THÊM MỚI: Route Sửa và Xóa
    Route::get('/accounts/{id}/edit', [AdminController::class, 'editAccount'])->name('accounts.edit'); // Form sửa
    Route::put('/accounts/{id}', [AdminController::class, 'updateAccount'])->name('accounts.update'); // Xử lý cập nhật
    Route::delete('/accounts/{id}', [AdminController::class, 'deleteAccount'])->name('accounts.destroy'); // Xử lý xóa

    Route::get('/products', [AdminController::class, 'products'])->name('products');

    // --- QUẢN LÝ DANH MỤC ---
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');


    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');


     Route::get('/promotions', [AdminController::class, 'promotions'])->name('promotions');
    // --- QUẢN LÝ THƯƠNG HIỆU ---
    Route::get('/brands', [AdminController::class, 'brands'])->name('brands'); // Thay cho route promotions cũ nếu muốn hoặc thêm mới
    Route::get('/brands/create', [AdminController::class, 'createBrand'])->name('brands.create');
    Route::post('/brands', [AdminController::class, 'storeBrand'])->name('brands.store');
    Route::get('/brands/{id}/edit', [AdminController::class, 'editBrand'])->name('brands.edit');
    Route::put('/brands/{id}', [AdminController::class, 'updateBrand'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminController::class, 'deleteBrand'])->name('brands.destroy');

    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');//trang cá nhân
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

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


// === PUBLIC ROUTES (Khách hàng) ===
// 1. Chi tiết sản phẩm & Tìm kiếm
// Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('product.detail');
// Route::get('/tim-kiem', [ProductController::class, 'search'])->name('search');

// // 2. Giỏ hàng
// Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
// Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
// Route::get('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');

// // 3. Thanh toán (Ví dụ bạn yêu cầu)
// Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout');
// Route::post('/thanh-toan', [CheckoutController::class, 'process'])->name('checkout.process');
// Route::get('/thanh-toan/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');


// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');
// Tìm kiếm sản phẩm
Route::get('/tim-kiem', [HomeController::class, 'search'])->name('search');

// Chi tiết sản phẩm
Route::get('/san-pham/{slug}', [HomeController::class, 'chiTiet'])->name('chi-tiet');

// Lọc theo thương hiệu
Route::get('/thuong-hieu/{slug}', [HomeController::class, 'thuongHieu'])->name('thuong-hieu');

// Lọc theo danh mục
Route::get('/danh-muc/{slug}', [HomeController::class, 'danhMuc'])->name('danh-muc');

// API cho trang chủ
Route::post('/binh-luan', [HomeController::class, 'themBinhLuan'])->name('binh-luan.them');
Route::post('/danh-gia', [HomeController::class, 'themDanhGia'])->name('danh-gia.them');

// Route cho giỏ hàng (cần tạo CartController riêng)
Route::middleware('auth')->group(function () {
    Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
    // Route::post('/yeu-thich/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Newsletter
// Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Quick view
Route::get('/san-pham/quick-view/{id}', [HomeController::class, 'quickView'])->name('product.quickview');

// 1. Xem chi tiết sản phẩm
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('product.detail');

// 2. Giỏ hàng
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/xoa', [CartController::class, 'remove'])->name('cart.remove');

// 3. Thanh toán (Yêu cầu đăng nhập)
Route::middleware(['auth'])->group(function () {
    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/thanh-toan', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/thanh-toan/thanh-cong', [CheckoutController::class, 'success'])->name('checkout.success');
});

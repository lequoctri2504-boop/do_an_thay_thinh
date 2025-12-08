<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NewsController;

// ==================== PUBLIC ROUTES ====================

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

    // QUẢN LÝ SẢN PHẨM
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.destroy');

    // QUẢN LÝ TÀI KHOẢN
    Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts');
    Route::get('/accounts/create', [AdminController::class, 'createAccount'])->name('accounts.create');
    Route::post('/accounts', [AdminController::class, 'storeAccount'])->name('accounts.store');
    Route::get('/accounts/{id}/edit', [AdminController::class, 'editAccount'])->name('accounts.edit');
    Route::put('/accounts/{id}', [AdminController::class, 'updateAccount'])->name('accounts.update');
    Route::delete('/accounts/{id}', [AdminController::class, 'deleteAccount'])->name('accounts.destroy');

    // DANH MỤC
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');

    // ĐƠN HÀNG
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}/edit', [AdminController::class, 'editOrder'])->name('orders.edit');
    Route::put('/orders/{id}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::delete('/orders/{id}', [AdminController::class, 'deleteOrder'])->name('orders.destroy');


    // KHUYẾN MÃI
    Route::get('/promotions', [AdminController::class, 'promotions'])->name('promotions');
    Route::get('/promotions/create', [AdminController::class, 'createPromotion'])->name('promotions.create');
    Route::post('/promotions', [AdminController::class, 'storePromotion'])->name('promotions.store');
    Route::get('/promotions/{id}/edit', [AdminController::class, 'editPromotion'])->name('promotions.edit');
    Route::put('/promotions/{id}', [AdminController::class, 'updatePromotion'])->name('promotions.update');
    Route::delete('/promotions/{id}', [AdminController::class, 'deletePromotion'])->name('promotions.destroy');
    
     // THƯƠNG HIỆU
    Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
    Route::get('/brands/create', [AdminController::class, 'createBrand'])->name('brands.create');
    Route::post('/brands', [AdminController::class, 'storeBrand'])->name('brands.store');
    Route::get('/brands/{id}/edit', [AdminController::class, 'editBrand'])->name('brands.edit');
    Route::put('/brands/{id}', [AdminController::class, 'updateBrand'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminController::class, 'deleteBrand'])->name('brands.destroy');

    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

// ==================== STAFF ROUTES ====================
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('dashboard');

    // FIX: Update staff orders route to include full CRUD
    Route::get('/orders', [StaffController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}/edit', [StaffController::class, 'editOrder'])->name('orders.edit');
    Route::put('/orders/{id}', [StaffController::class, 'updateOrder'])->name('orders.update');
    Route::delete('/orders/{id}', [StaffController::class, 'deleteOrder'])->name('orders.destroy');


    Route::get('/products', [StaffController::class, 'products'])->name('products');
    Route::get('/products/{id}/edit', [StaffController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [StaffController::class, 'updateProduct'])->name('products.update');

    Route::post('/products/{id}/toggle-flag', [StaffController::class, 'toggleProductFlag'])->name('products.toggle_flag');


    Route::get('/customers', [StaffController::class, 'customers'])->name('customers');
    Route::get('/support', [StaffController::class, 'support'])->name('support');
    Route::get('/reports', [StaffController::class, 'reports'])->name('reports');

    // QUẢN LÝ TIN CÔNG NGHỆ (MỚI)
    Route::get('/news', [StaffController::class, 'news'])->name('news');
    Route::get('/news/create', [StaffController::class, 'createNews'])->name('news.create');
    Route::post('/news', [StaffController::class, 'storeNews'])->name('news.store');
    Route::get('/news/{id}/edit', [StaffController::class, 'editNews'])->name('news.edit');
    Route::put('/news/{id}', [StaffController::class, 'updateNews'])->name('news.update');
    Route::delete('/news/{id}', [StaffController::class, 'deleteNews'])->name('news.destroy');
});

// ==================== CUSTOMER ROUTES ====================
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/wishlist', [CustomerController::class, 'wishlist'])->name('wishlist');
    Route::get('/reviews', [CustomerController::class, 'reviews'])->name('reviews');
});


// ============================================================================
// ========================== BẮT ĐẦU FILE 2  =================================
// ============================================================================

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;

// ==================== SẢN PHẨM ====================
Route::prefix('san-pham')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/tim-kiem', [ProductController::class, 'search'])->name('search');
    // THÊM ROUTE CHO SẢN PHẨM NỔI BẬT
    Route::get('/noi-bat', [ProductController::class, 'featuredProducts'])->name('featured'); 
    Route::get('/danh-muc/{slug}', [ProductController::class, 'category'])->name('category');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');

    
});

// ==================== THƯƠNG HIỆU ====================
Route::get('/thuong-hieu', function () {
    return view('brands.index');
})->name('brands.index');

Route::get('/thuong-hieu/{slug}', [ProductController::class, 'brand'])->name('brands.show');

// ==================== GIỎ HÀNG ====================
Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/them', [CartController::class, 'add'])->name('add');
    Route::put('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    
    // FIX LỖI: Thêm route áp dụng mã giảm giá
    Route::post('/ap-ma-giam-gia', [CartController::class, 'applyDiscount'])->name('apply.discount');
});

// ==================== YÊU CẦU ĐĂNG NHẬP ====================
Route::middleware(['auth'])->group(function () {

    // Đơn hàng
    Route::prefix('don-hang')->name('orders.')->group(function () {
        Route::get('/thanh-toan', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/dat-hang', [OrderController::class, 'place'])->name('place');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/huy', [OrderController::class, 'cancel'])->name('cancel');
        
        // Route áp dụng mã giảm giá trên trang checkout (để dự phòng)
        Route::post('/ap-ma-giam-gia', [OrderController::class, 'applyDiscount'])->name('apply.discount');
    });

    // Thanh toán ZaloPay
    Route::prefix('thanh-toan')->name('payment.')->group(function () {
    // 1. Route tạo giao dịch (vẫn dùng route name cũ, nhưng logic là VNPAY)
    Route::get('/vnpay/create/{order_id}', [OrderController::class, 'zalopayCreate'])->name('zalopay.create');
    
    // 2. Route Return URL (Browser Redirect) - Dùng GET
    Route::get('/vnpay/return', [OrderController::class, 'vnpayReturn'])->name('vnpay.return');
    
    // 3. Route IPN (Server-to-Server Notification) - Dùng POST
    Route::post('/vnpay/ipn', [OrderController::class, 'vnpayIPN'])->name('vnpay.ipn');
});

    // Tài khoản khách hàng
    Route::prefix('tai-khoan')->name('customer.')->group(function () {
        Route::get('/thong-tin', [CustomerController::class, 'profile'])->name('profile');
        Route::put('/cap-nhat', [CustomerController::class, 'updateProfile'])->name('update');
        Route::get('/don-hang', [CustomerController::class, 'orders'])->name('orders');
        Route::get('/wishlist', [CustomerController::class, 'wishlist'])->name('wishlist');
        Route::get('/dia-chi', [CustomerController::class, 'addresses'])->name('addresses');
        Route::post('/dia-chi', [CustomerController::class, 'addAddress'])->name('addresses.add');
        Route::put('/dia-chi/{id}', [CustomerController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/dia-chi/{id}', [CustomerController::class, 'deleteAddress'])->name('addresses.delete');
    });

    // Yêu thích
    Route::prefix('yeu-thich')->name('wishlist.')->group(function () {
        Route::post('/them', [WishlistController::class, 'add'])->name('add');
        Route::delete('/{id}', [WishlistController::class, 'remove'])->name('remove');
        Route::post('/{id}/chuyen-gio-hang', [WishlistController::class, 'moveToCart'])->name('moveToCart');
        Route::delete('/', [WishlistController::class, 'clear'])->name('clear');
    });
});

// ==================== TRANG TĨNH ====================
Route::get('/lien-he', fn() => view('pages.contact'))->name('contact');
Route::get('/gioi-thieu', fn() => view('pages.about'))->name('about');
Route::get('/chinh-sach-bao-hanh', fn() => view('pages.warranty'))->name('warranty');
Route::get('/chinh-sach-doi-tra', fn() => view('pages.return'))->name('return');
Route::get('/huong-dan-mua-hang', fn() => view('pages.guide'))->name('guide');
Route::get('/chinh-sach-bao-mat', fn() => view('pages.privacy'))->name('privacy');
Route::get('/dieu-khoan-su-dung', fn() => view('pages.terms'))->name('terms');
Route::get('/khuyen-mai', fn() => view('pages.promotions'))->name('promotions');

// ==================== TIN TỨC CÔNG NGHỆ ====================
Route::prefix('tin-tuc')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});
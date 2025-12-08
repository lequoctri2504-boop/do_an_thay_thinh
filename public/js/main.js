// ==================== GLOBAL VARIABLES (Đã loại bỏ/commented out) ====================

// ==================== DOM READY ====================
document.addEventListener('DOMContentLoaded', function() {
    
    // Các lệnh này sẽ gọi các hàm an toàn đã được sửa bên dưới
    updateCartBadge();
    updateWishlistBadge();
    
    // Product tabs
    initProductTabs();
    
    // Account menu
    initAccountMenu();
    
    // Dashboard navigation
    initSidebarMenu();
    initUserDropdown(); // Khởi tạo Dropdown (Đã Fix)
    
    // Add to cart buttons
    // Hàm này đã được làm sạch để không gây lỗi ReferenceError
    initAddToCartButtons(); 
    
    // Wishlist buttons
    // Hàm này đã được làm sạch để không gây lỗi ReferenceError
    initWishlistButtons(); 
    
    // Thumbnail gallery
    initThumbnailGallery();
    
    // Quantity selectors
    initQuantitySelectors();
    
    // Color and storage options
    initProductOptions();
});

// ==================== CART FUNCTIONS (FIXED - Tránh ReferenceError) ====================
function updateCartBadge() {
    // FIX: Chỉ là hàm stub để tránh lỗi ReferenceError khi được gọi trong DOMContentLoaded. 
    // Logic cập nhật thực tế nằm trong các view AJAX.
    const badges = document.querySelectorAll('.cart-btn .badge');
}

function updateWishlistBadge() {
    // FIX: Chỉ là hàm stub để tránh lỗi ReferenceError.
    const badges = document.querySelectorAll('.wishlist-btn .badge');
}

function initAddToCartButtons() {
    const addToCartBtns = document.querySelectorAll('.btn-cart');
    addToCartBtns.forEach(btn => {
        // Nội dung hàm này bị vô hiệu hóa để tránh ReferenceError (vì nó sử dụng biến 'cart' toàn cục)
        // và để không xung đột với logic AJAX Laravel trong các view.
        btn.addEventListener('click', function(e) {
            e.preventDefault();
        });
    });
}

function initWishlistButtons() {
    const wishlistBtns = document.querySelectorAll('.wishlist-icon, .btn-wishlist');
    wishlistBtns.forEach(btn => {
        // Nội dung hàm này bị vô hiệu hóa để tránh ReferenceError
        btn.addEventListener('click', function(e) {
            e.preventDefault();
        });
    });
}

// ==================== TOAST NOTIFICATION ====================
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.className = 'toast show';
    
    if (type === 'error') {
        toast.style.background = '#DC3545';
    } else if (type === 'info') {
        toast.style.background = '#17A2B8';
    } else {
        toast.style.background = '#28A745';
    }
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// ==================== PRODUCT TABS ====================
function initProductTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            tabBtns.forEach(tb => tb.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetTab)?.classList.add('active');
        });
    });
}

// ==================== ACCOUNT MENU ====================
function initAccountMenu() {
    const menuItems = document.querySelectorAll('.account-menu .menu-item');
    const contentSections = document.querySelectorAll('.content-section');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            
            menuItems.forEach(mi => mi.classList.remove('active'));
            contentSections.forEach(cs => cs.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetSection)?.classList.add('active');
        });
    });
}

// ==================== DASHBOARD NAVIGATION ====================

function initSidebarMenu() {
    const triggers = document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href') || this.getAttribute('data-bs-target');
            const targetMenu = document.querySelector(targetId);

            if (targetMenu) {
                if (targetMenu.classList.contains('show')) {
                    targetMenu.classList.remove('show');
                    this.classList.add('collapsed');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    targetMenu.classList.add('show');
                    this.classList.remove('collapsed');
                    this.setAttribute('aria-expanded', 'true');
                }
            }
        });
    });
}

// ==================== USER DROPDOWN (FIXED VÀ ĐÃ TEST) ====================
function initUserDropdown() {
    const userBtn = document.querySelector('.dashboard-user .user-btn'); 
    const userMenu = document.querySelector('.user-dropdown .dropdown-menu'); 
    
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); 
            userMenu.classList.toggle('show'); 
        });

        document.addEventListener('click', function(e) {
            const userDropdown = userBtn.closest('.user-dropdown');
            if (userDropdown && !userDropdown.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });
    }
}


// ==================== THUMBNAIL GALLERY ====================
function initThumbnailGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail-list img');
    const mainImage = document.getElementById('mainImg');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked thumbnail
            this.classList.add('active');
            
            // Update main image
            if (mainImage) {
                mainImage.src = this.src.replace('w=100&h=100', 'w=600&h=600');
            }
        });
    });
}

// ==================== QUANTITY SELECTORS ====================
function initQuantitySelectors() {
    const quantitySelectors = document.querySelectorAll('.item-quantity');
    
    quantitySelectors.forEach(selector => {
        const minusBtn = selector.querySelector('.qty-btn:first-child');
        const plusBtn = selector.querySelector('.qty-btn:last-child');
        const input = selector.querySelector('input');
        
        if (minusBtn && input) {
            minusBtn.addEventListener('click', function() {
                let value = parseInt(input.value) || 1;
                if (value > 1) {
                    input.value = value - 1;
                }
            });
        }
        
        if (plusBtn && input) {
            plusBtn.addEventListener('click', function() {
                let value = parseInt(input.value) || 1;
                input.value = value + 1;
            });
        }
    });
}

// ==================== PRODUCT OPTIONS ====================
function initProductOptions() {
    // Color buttons
    const colorBtns = document.querySelectorAll('.color-btn');
    colorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            colorBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update selected option text
            const selectedColor = this.getAttribute('data-color');
            const selectedOption = this.closest('.option-group').querySelector('.selected-option');
            if (selectedOption) {
                selectedOption.textContent = selectedColor;
            }
        });
    });
    
    // Storage buttons
    const storageBtns = document.querySelectorAll('.storage-btn');
    storageBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            storageBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// ==================== CHAT WIDGET ====================
const chatToggle = document.querySelector('.chat-toggle');
if (chatToggle) {
    chatToggle.addEventListener('click', function() {
        showToast('Tính năng chat đang được phát triển!', 'info');
    });
}

// ==================== CONSOLE MESSAGE ====================
console.log('%c🚀 PhoneShop Website', 'color: #D70018; font-size: 24px; font-weight: bold;');

// ==================== HELPER FUNCTIONS ====================
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
}

// ==================== EXPORT FOR GLOBAL USE ====================
window.PhoneShop = {
    showToast,
    formatCurrency,
    formatDate,
};
/**
 * ===============================================
 * HOME PAGE JAVASCRIPT - PhoneShop
 * ===============================================
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // ===============================================
    // BANNER SLIDER
    // ===============================================
    initBannerSlider();
    
    // ===============================================
    // COUNTDOWN TIMER
    // ===============================================
    initCountdownTimer();
    
    // ===============================================
    // PRODUCT INTERACTIONS
    // ===============================================
    initProductInteractions();
    
    // ===============================================
    // SCROLL ANIMATIONS
    // ===============================================
    initScrollAnimations();
    
    // ===============================================
    // LAZY LOADING IMAGES
    // ===============================================
    initLazyLoading();
});

/**
 * Initialize Banner Slider
 */
// function initBannerSlider() {
//     const sliderWrapper = document.querySelector('.slider-wrapper');
//     if (!sliderWrapper) return;
    
//     const slides = document.querySelectorAll('.slide');
//     if (slides.length === 0) return;
    
//     let currentSlide = 0;
//     const slideCount = slides.length;
    
//     // Create navigation dots
//     createSliderDots(slideCount);
    
//     // Auto slide every 5 seconds
//     setInterval(() => {
//         nextSlide();
//     }, 5000);
    
//     function nextSlide() {
//         slides[currentSlide].classList.remove('active');
//         currentSlide = (currentSlide + 1) % slideCount;
//         slides[currentSlide].classList.add('active');
//         updateDots();
//     }
    
//     function goToSlide(index) {
//         slides[currentSlide].classList.remove('active');
//         currentSlide = index;
//         slides[currentSlide].classList.add('active');
//         updateDots();
//     }
    
//     function createSliderDots(count) {
//         const dotsContainer = document.createElement('div');
//         dotsContainer.className = 'slider-dots';
        
//         for (let i = 0; i < count; i++) {
//             const dot = document.createElement('span');
//             dot.className = 'slider-dot';
//             if (i === 0) dot.classList.add('active');
//             dot.addEventListener('click', () => goToSlide(i));
//             dotsContainer.appendChild(dot);
//         }
        
//         sliderWrapper.appendChild(dotsContainer);
//     }
    
//     function updateDots() {
//         const dots = document.querySelectorAll('.slider-dot');
//         dots.forEach((dot, index) => {
//             if (index === currentSlide) {
//                 dot.classList.add('active');
//             } else {
//                 dot.classList.remove('active');
//             }
//         });
//     }
// }
function initBannerSlider() {
    const sliderWrapper = document.querySelector('.slider-wrapper');
    if (!sliderWrapper) return;
    
    const slides = document.querySelectorAll('.slide');
    if (slides.length === 0) return;
    
    let currentSlide = 0;
    const slideCount = slides.length;
    
    const prevArrow = document.querySelector('.arrow-left');
    const nextArrow = document.querySelector('.arrow-right');
    
    // Bắt đầu interval tự động chuyển slide sau 3 giây (3000ms)
    let autoSlideInterval = setInterval(nextSlide, 3000); 

    // Hàm reset interval sau khi người dùng tương tác
    function resetInterval() {
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextSlide, 3000);
    }
    
    function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slideCount;
        slides[currentSlide].classList.add('active');
        updateDots();
    }
    
    function prevSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide - 1 + slideCount) % slideCount; 
        slides[currentSlide].classList.add('active');
        updateDots();
    }
    
    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        currentSlide = index;
        slides[currentSlide].classList.add('active');
        updateDots();
    }
    
    // Event listeners cho arrows
    if (prevArrow && nextArrow) {
        prevArrow.addEventListener('click', function() {
            prevSlide();
            resetInterval();
        });
        nextArrow.addEventListener('click', function() {
            nextSlide();
            resetInterval();
        });
    }

    // Create navigation dots (với logic reset interval)
    createSliderDots(slideCount);
    
    function createSliderDots(count) {
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'slider-dots';
        
        for (let i = 0; i < count; i++) {
            const dot = document.createElement('span');
            dot.className = 'slider-dot';
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToSlide(i);
                resetInterval(); 
            }); 
            dotsContainer.appendChild(dot);
        }
        
        sliderWrapper.appendChild(dotsContainer);
    }
    
    function updateDots() {
        const dots = document.querySelectorAll('.slider-dot');
        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
}
/**
 * Initialize Countdown Timer
 */
function initCountdownTimer() {
    const countdownElement = document.querySelector('.countdown');
    if (!countdownElement) return;
    
    // Set target time (example: 3 hours from now)
    const targetTime = new Date().getTime() + (3 * 60 * 60 * 1000);
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetTime - now;
        
        if (distance < 0) {
            countdownElement.innerHTML = '<span>00</span>:<span>00</span>:<span>00</span>';
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        const spans = countdownElement.querySelectorAll('span');
        if (spans.length >= 3) {
            spans[0].textContent = hours.toString().padStart(2, '0');
            spans[1].textContent = minutes.toString().padStart(2, '0');
            spans[2].textContent = seconds.toString().padStart(2, '0');
        }
    }
    
    // Update every second
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

/**
 * Initialize Product Interactions
 */
function initProductInteractions() {
    // Add to wishlist functionality
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                showToast('Đã thêm vào yêu thích', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                showToast('Đã xóa khỏi yêu thích', 'info');
            }
        });
    });
    
    // Quick view functionality
    const quickViewButtons = document.querySelectorAll('.quick-view');
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            // If it's a link, let it navigate
            if (this.tagName === 'A') {
                window.location.href = this.href;
            } else {
                console.log('Quick view clicked');
                // Add your quick view modal logic here
            }
        });
    });
}

/**
 * Initialize Scroll Animations
 */
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe product cards
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.5s ease ${index * 0.1}s`;
        observer.observe(card);
    });
}

/**
 * Initialize Lazy Loading for Images
 */
function initLazyLoading() {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });
    
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => imageObserver.observe(img));
}

/**
 * Show Toast Notification
 */
function showToast(message, type = 'info') {
    // Remove existing toast
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    
    // Style the toast
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.3s ease-out;
        font-weight: 500;
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Format Currency (VND)
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

/**
 * Smooth Scroll to Element
 */
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

/**
 * Debounce Function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export functions for use in other scripts
window.PhoneShop = {
    showToast,
    formatCurrency,
    smoothScrollTo,
    debounce
};
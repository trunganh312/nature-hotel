function initHeaderJS() {
    try {
        // jQuery for dropdown submenu on hover for desktop
        
        $('.dropdown-submenu').hover(function() {
            if ($(window).width() > 991) {
                $(this).find('.dropdown-menu').first().stop(true, true).slideDown(200);
            }
        }, function() {
            if ($(window).width() > 991) {
                $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
            }
        });

        // Toggle right content on mobile
        $('.click-nav-right-icon').click(function() {
            $('.navbar-right-content').slideToggle(200);
        });

        let lastScroll = 0;
        const header = document.querySelector('.header-style-modern');
        const scrollThreshold = 400;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > scrollThreshold) {
                if (currentScroll > lastScroll) {
                    // Cuộn xuống
                    header.classList.add('header-hidden');
                } else {
                    // Cuộn lên
                    header.classList.remove('header-hidden');
                }
            } else {
                header.classList.remove('header-hidden');
            }
            
            lastScroll = currentScroll;
        });
    } catch (error) {
        console.error('Error in header.js:', error);
    }
}

// Kiểm tra jQuery đã load chưa
if (window.jQuery) {
    $(document).ready(initHeaderJS);
} else {
    // Tạo event listener chờ jQuery load xong
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initHeaderJS);
        }
    }, 100);
}
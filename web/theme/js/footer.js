function initFooterJS() {
    try {
        // jQuery for footer interactions
        $(document).ready(function() {
            // Fade-in animation for footer content
            $('.footer_content').css('opacity', 0).animate({ opacity: 1 }, 800);

            // Hover effect for social icons
            $('.footer-social-link').hover(
                function() {
                    $(this).css('color', 'var(--accent-color)');
                },
                function() {
                    $(this).css('color', 'var(--white)');
                }
            );
        });
    } catch (error) {
        console.error('Error in footer.js:', error);
    }
}

// Kiểm tra jQuery đã load chưa
if (window.jQuery) {
    $(document).ready(initFooterJS);
} else {
    // Tạo event listener chờ jQuery load xong
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initFooterJS);
        }
    }, 100);
}
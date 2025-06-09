function initOverviewDetailJS() {
    try {
        // Khởi tạo Owl Carousel cho gallery chính
        const mainCarousel = $("#galleryCarouselMain").owlCarousel({
            items: 1,
            loop: false,
            nav: true,
            dots: false,
            margin: 0,
            navText: [
                '<i class="fa fa-chevron-left"></i>',
                '<i class="fa fa-chevron-right"></i>'
            ]
        });

        // Khởi tạo Owl Carousel cho thumbnails
        const thumbCarousel = $("#galleryCarouselThumbs").owlCarousel({
            items: 6,
            loop: false,
            nav: true,
            dots: false,
            margin: 10,
            navText: [
                '<i class="fa fa-chevron-left"></i>',
                '<i class="fa fa-chevron-right"></i>'
            ],
            responsive: {
                0: { items: 3 },
                600: { items: 4 },
                1000: { items: 6 }
            }
        });

        // Xử lý khi click vào ảnh trong gallery
        $('.hotel-gallery img, .gallery-overlay').on('click', function() {
            const index = $(this).closest('.col-6').index();
            $('#hotelGalleryModal').modal('show');
            
            // Di chuyển carousel chính đến ảnh được click
            mainCarousel.trigger('to.owl.carousel', [index, 300]);
            
            // Di chuyển thumbnail và đảm bảo ảnh active ở giữa
            syncThumbnail(index);
        });

        // Xử lý khi click vào thumbnail
        thumbCarousel.on('click', '.owl-item', function() {
            const index = $(this).index();
            mainCarousel.trigger('to.owl.carousel', [index, 300]);
            syncThumbnail(index);
        });

        // Đồng bộ thumbnail khi carousel chính thay đổi
        mainCarousel.on('changed.owl.carousel', function(event) {
            const index = event.item.index;
            syncThumbnail(index);
        });

        // Hàm đồng bộ thumbnail
        function syncThumbnail(index) {
            // Xóa class current khỏi tất cả thumbnails
            thumbCarousel.find('.owl-item').removeClass('current');
            
            // Thêm class current cho thumbnail tương ứng
            thumbCarousel.find('.owl-item').eq(index).addClass('current');
            
            // Tính toán vị trí để thumbnail active ở giữa
            const thumbCount = thumbCarousel.find('.owl-item').length;
            const visibleThumbs = thumbCarousel.find('.owl-item').eq(0).closest('.owl-carousel').data('owl.carousel').settings.items;
            let targetIndex = index - Math.floor(visibleThumbs / 2);
            
            // Đảm bảo targetIndex không vượt quá giới hạn
            if (targetIndex < 0) targetIndex = 0;
            if (targetIndex > thumbCount - visibleThumbs) {
                targetIndex = thumbCount - visibleThumbs;
            }
            
            // Di chuyển thumbnail carousel
            thumbCarousel.trigger('to.owl.carousel', [targetIndex, 300]);
        }

        // Xử lý play/pause video khi click
        $('.hotel-video').on('click', function() {
            const video = $(this).find('video')[0];
            if (video.paused) {
                video.play();
                $(this).find('.play-icon').fadeOut(200);
            } else {
                video.pause();
                $(this).find('.play-icon').fadeIn(200);
            }
        });

    } catch (error) {
        console.error('Error in overview_detail.js:', error);
    }
}

// Kiểm tra jQuery đã load chưa
if (window.jQuery) {
    $(document).ready(initOverviewDetailJS);
} else {
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initOverviewDetailJS);
        }
    }, 100);
}
function initListJS() {
    try {
    // Khởi tạo Owl Carousel cho danh sách khách sạn
    $(document).ready(function() {
        // Khởi tạo Owl Carousel cho mỗi khách sạn
        $('.owl-carousel').each(function() {
            $(this).owlCarousel({
            items: 1,
            loop: true,
            margin: 10,
            nav: true,
            dots: true,
            autoplay: false,
            autoplayHoverPause: true,
            navText: [
                '<i class="fas fa-chevron-left"></i>',
                '<i class="fas fa-chevron-right"></i>'
            ]
            });
        });

        // Xử lý nút yêu thích
        $(document).on('click', '.favorite-btn', function(e) {
            e.preventDefault();
            const $icon = $(this).find('i');
            $icon.toggleClass('far fas'); // Chuyển đổi giữa rỗng và đầy
        });
        // Dữ liệu tĩnh mẫu cho thành phố
        const cities = [
          { city_id: 1, city_name: 'Hà Nội' },
          { city_id: 2, city_name: 'TP. Hồ Chí Minh' },
          { city_id: 3, city_name: 'Đà Nẵng' },
          { city_id: 4, city_name: 'Huế' },
          { city_id: 5, city_name: 'Nha Trang' }
        ];
      
        // Dữ liệu tĩnh mẫu cho tags (tiện nghi và dịch vụ)
        const tags = [
          { tag_id: 1, tag_name: 'Wi-Fi miễn phí' },
          { tag_id: 2, tag_name: 'Bể bơi' },
          { tag_id: 3, tag_name: 'Phòng gym' },
          { tag_id: 4, tag_name: 'Bữa sáng miễn phí' },
          { tag_id: 5, tag_name: 'Dịch vụ spa' }
        ];
      
        let offsetCities = 3; // Hiển thị 3 thành phố ban đầu
        let offsetTags = 3;   // Hiển thị 3 tags ban đầu
      
        // Hàm render ban đầu
        function renderCities() {
          const citiesDiv = $('.cities');
          citiesDiv.empty();
          cities.slice(0, offsetCities).forEach(city => {
            const radio = `
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="city" id="city_${city.city_id}" value="${city.city_id}">
                <label class="form-check-label" for="city_${city.city_id}">${city.city_name}</label>
              </div>
            `;
            citiesDiv.append(radio);
          });
        }
      
        function renderTags() {
          const tagsDiv = $('.tags');
          tagsDiv.empty();
          tags.slice(0, offsetTags).forEach(tag => {
            const checkbox = `
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="tags[]" id="tag_${tag.tag_id}" value="${tag.tag_id}">
                <label class="form-check-label" for="tag_${tag.tag_id}">${tag.tag_name}</label>
              </div>
            `;
            tagsDiv.append(checkbox);
          });
        }
      
        // Render ban đầu
        renderCities();
        renderTags();
      
        // Xử lý nút Xem thêm/Rút gọn cho thành phố
        const loadMoreCitiesButton = $('#load-more-cities');
        loadMoreCitiesButton.on('click', function() {
          const citiesDiv = $('.cities');
          if (offsetCities < cities.length) {
            offsetCities = cities.length;
            renderCities();
            loadMoreCitiesButton.text('Rút gọn');
          } else {
            offsetCities = 3;
            renderCities();
            loadMoreCitiesButton.text('Xem thêm');
          }
        });
      
        // Xử lý nút Xem thêm/Rút gọn cho tags
        const loadMoreTagsButton = $('#load-more-tags');
        loadMoreTagsButton.on('click', function() {
          const tagsDiv = $('.tags');
          if (offsetTags < tags.length) {
            offsetTags = tags.length;
            renderTags();
            loadMoreTagsButton.text('Rút gọn');
          } else {
            offsetTags = 3;
            renderTags();
            loadMoreTagsButton.text('Xem thêm');
          }
        });
      
        // Xử lý sự kiện chọn thành phố
        $('.cities').on('change', 'input[name="city"]', function() {
          const value = $(this).val();
          const text = $(this).next('label').text().trim();
          console.log(`Thành phố được chọn: ${text} (ID: ${value})`);
          // Thêm logic của bạn tại đây
        });
      
        // Xử lý sự kiện chọn tags
        $('.tags').on('change', 'input[name="tags[]"]', function() {
          const selectedTags = $('.tags input[name="tags[]"]:checked')
            .map(function() {
              return $(this).next('label').text().trim();
            })
            .get();
          console.log(`Tags được chọn: ${selectedTags.join(', ')}`);
          // Thêm logic của bạn tại đây
        });
      
        // Xử lý nút Xóa
        $('.filter-clear').on('click', function() {
          const filterType = $(this).data('filter');
          if (filterType === 'city') {
            $('.cities input[name="city"]').prop('checked', false);
          } else if (filterType === 'tags') {
            $('.tags input[name="tags[]"]').prop('checked', false);
          }
          $(this).removeClass('visible');
          console.log(`${filterType} đã được xóa`);
          // Thêm logic của bạn tại đây
        });
      
        // Hiển thị nút Xóa khi có lựa chọn
        $('.cities, .tags').on('change', 'input', function() {
          $('.filter-clear[data-filter="city"]').toggleClass('visible', $('.cities input:checked').length > 0);
          $('.filter-clear[data-filter="tags"]').toggleClass('visible', $('.tags input:checked').length > 0);
        });
      });
    } catch (error) {
        console.error('Error in list.js:', error);
    }
}

// Kiểm tra jQuery đã load chưa
if (window.jQuery) {
    $(document).ready(initListJS);
} else {
    // Tạo event listener chờ jQuery load xong
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initListJS);
        }
    }, 100);
}
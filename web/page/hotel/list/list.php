<?
include('data_list.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <?= $Layout->loadHead() ?>
</head>

<body class="page_hotel_list header_white page-template page st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>

    <?
    include('view_list.php');
    ?>

    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    <?= $Layout->loadFooter() ?>
    <script>
        function initListJS() {
            try {
                // Khởi tạo Owl Carousel cho danh sách khách sạn
                $(document).ready(function() {
                    // Khởi tạo Owl Carousel cho mỗi khách sạn
                    $('.list-carousel').each(function() {
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

                    const cities = <?php echo json_encode($cities); ?>;
                    const selectedCityId = <?= (int)$id ?>;

                    // Dữ liệu tiện nghi động từ PHP
                    const tags = <?php echo json_encode($amenities, JSON_UNESCAPED_UNICODE); ?>;

                    let offsetCities = 100; // Hiển thị 3 thành phố ban đầu
                    let offsetTags = 100; // Hiển thị 3 tags ban đầu

                    // Hàm render ban đầu
                    function renderCities() {
                        const citiesDiv = $('.cities');
                        citiesDiv.empty();
                        cities.slice(0, offsetCities).forEach(city => {
                            const checked = (city.city_id == selectedCityId) ? 'checked' : '';
                            const radio = `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="city" id="city_${city.city_id}" value="${city.city_id}" data-link="${city.city_link}" ${checked}>
                        <label class="form-check-label" for="city_${city.city_id}">${city.city_name}</label>
                    </div>
                    `;
                            citiesDiv.append(radio);
                        });
                        // Hiển thị nút Xóa nếu có radio được checked
                        $('.filter-clear[data-filter="city"]').toggleClass('visible', $('.cities input[name="city"]:checked').length > 0);
                    }

                    function renderTags() {
                        const tagsDiv = $('.tags');
                        // Lưu lại các tag đang được chọn trước khi render lại
                        const checkedTags = $('.tags input[name="tags[]"]:checked').map(function() {
                            return $(this).val();
                        }).get();
                        tagsDiv.empty();
                        tags.slice(0, offsetTags).forEach(tag => {
                            const checked = checkedTags.includes(String(tag.tag_id)) ? 'checked' : '';
                            const checkbox = `
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="tags[]" id="tag_${tag.tag_id}" value="${tag.tag_id}" ${checked}>
                                                <label class="form-check-label" for="tag_${tag.tag_id}">${tag.tag_name}</label>
                                            </div>
                                            `;
                            tagsDiv.append(checkbox);
                        });
                        // Hiển thị nút Xóa tiện ích nếu có ít nhất 1 tiện ích được chọn
                        if (checkedTags.length > 0) {
                            $('.filter-clear[data-filter="tags"]').addClass('visible');
                        } else {
                            $('.filter-clear[data-filter="tags"]').removeClass('visible');
                        }
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
                        const link = $(this).data('link');
                        if (link) {
                            window.location.href = link;
                            return;
                        }
                        const value = $(this).val();
                        const text = $(this).next('label').text().trim();
                        console.log(`Thành phố được chọn: ${text} (ID: ${value})`);
                        // Thêm logic của bạn tại đây
                    });

                    // Xử lý sự kiện chọn tags (lọc đồng thời cả city và tags)
                    $('.tags').on('change', 'input[name="tags[]"]', function() {
                        // Lấy danh sách tag_id đang chọn
                        const selectedTags = $('.tags input[name="tags[]"]:checked')
                            .map(function() {
                                return $(this).val();
                            }).get();
                        // Lấy link city hiện tại (nếu có)
                        let baseUrl = '/khach-san.html';
                        if (selectedCityId && cities.length > 0) {
                            const city = cities.find(c => c.city_id == selectedCityId);
                            if (city && city.city_link) {
                                baseUrl = city.city_link;
                            }
                        }
                        // Nếu có tags thì build URL với ?tags=...
                        let url = baseUrl;
                        if (selectedTags.length > 0) {
                            url += '?tags=' + selectedTags.join(',');
                        }
                        // Chuyển hướng
                        window.location.href = url;
                    });

                    // Khi load trang, nếu có tags đã chọn thì set checked cho các checkbox
                    $(document).ready(function() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const tagsParam = urlParams.get('tags');
                        if (tagsParam) {
                            const selectedTagArr = tagsParam.split(',');
                            selectedTagArr.forEach(tagId => {
                                $("#tag_" + tagId).prop('checked', true);
                            });
                        }
                    });

                    // Xử lý nút Xóa
                    $('.filter-clear').on('click', function() {
                        const filterType = $(this).data('filter');
                        if (filterType === 'city') {
                            $('.cities input[name="city"]').prop('checked', false);
                            // Lấy tags đang chọn
                            const selectedTags = $('.tags input[name="tags[]"]:checked').map(function() {
                                return $(this).val();
                            }).get();
                            let url = '/khach-san.html';
                            if (selectedTags.length > 0) {
                                url += '?tags=' + selectedTags.join(',');
                            }
                            window.location.href = url;
                            return;
                        } else if (filterType === 'tags') {
                            $('.tags input[name="tags[]"]').prop('checked', false);
                        }
                        $(this).removeClass('visible');
                        console.log(`${filterType} đã được xóa`);
                        // Thêm logic của bạn tại đây
                    });

                    // Hiển thị nút Xóa khi có lựa chọn
                    $('.cities, .tags').on('change', 'input', function() {
                        // Nút Xóa city
                        $('.filter-clear[data-filter="city"]').toggleClass('visible', $('.cities input[name="city"]:checked').length > 0);
                        // Nút Xóa tags
                        $('.filter-clear[data-filter="tags"]').toggleClass('visible', $('.tags input[name="tags[]"]:checked').length > 0);
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
    </script>
</body>

</html>
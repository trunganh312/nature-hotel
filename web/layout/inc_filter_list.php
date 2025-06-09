<div class="col-md-3">
  <div class="col_left filter-list">
    <!-- Nút hiển thị bộ lọc trên mobile -->
    <button class="btn btn-primary filter-toggle d-md-none">Chọn bộ lọc</button>

    <!-- Container cho bộ lọc -->
    <div class="filter-container">
    <!-- Bộ lọc Thành phố -->
    <div class="filter-section">
      <div class="filter-header d-flex justify-content-between">
        <h6 class="filter-title">Thành phố</h6>
        <div class="filter-clear" data-filter="city">Xóa</div>
      </div>
        <ul class="list_filter cities">
          <?php foreach ($cities as $city): ?>
            <li>
              <a href="<?= htmlspecialchars($city['city_link']) ?>" 
                 class="filter-item <?= in_array($city['city_id'], explode(',', getValue('city'))) ? 'active' : '' ?>" 
                 data-city="<?= htmlspecialchars($city['city_id']) ?>">
                <?= htmlspecialchars($city['city_name']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <button id="load-more-cities" class="btn btn-link">Xem thêm</button>
      </div>

      <!-- Bộ lọc Tiện ích -->
      <div class="filter-section">
        <div class="filter-header">
          <h6 class="filter-title">Tiện ích</h6>
        </div>
        <ul class="list_filter tags">
          <?php foreach ($amenities as $amenity): ?>
            <li>
              <a href="#" 
                 class="filter-item <?= in_array($amenity['tag_id'], explode(',', getValue('tags'))) ? 'active' : '' ?>" 
                 data-tag="<?= htmlspecialchars($amenity['tag_id']) ?>">
                <?= htmlspecialchars($amenity['tag_name']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <button id="load-more-tags" class="btn btn-link">Xem thêm</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal bộ lọc cho mobile -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Bộ lọc</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="filter-section">
          <div class="filter-header d-flex justify-content-between">
            <h6 class="filter-title">Thành phố</h6>
            <div class="filter-clear" data-filter="city">Xóa</div>
          </div>
          <ul class="list_filter cities">
            <?php foreach ($cities as $city): ?>
              <li>
                <a href="<?= htmlspecialchars($city['city_link']) ?>" 
                   class="filter-item <?= in_array($city['city_id'], explode(',', getValue('city'))) ? 'active' : '' ?>" 
                   data-city="<?= htmlspecialchars($city['city_id']) ?>">
                  <?= htmlspecialchars($city['city_name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <button id="load-more-cities-mobile" class="btn btn-link">Xem thêm</button>
    </div>

    <!-- Bộ lọc Tiện nghi và Dịch vụ đi kèm (Tags) -->
    <div class="filter-section">
      <div class="filter-header">
        <h6 class="filter-title">Tiện ích</h6>
      </div>
          <ul class="list_filter tags">
            <?php foreach ($amenities as $amenity): ?>
              <li>
                <a href="#" 
                   class="filter-item <?= in_array($amenity['tag_id'], explode(',', getValue('tags'))) ? 'active' : '' ?>" 
                   data-tag="<?= htmlspecialchars($amenity['tag_id']) ?>">
                  <?= htmlspecialchars($amenity['tag_name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <button id="load-more-tags-mobile" class="btn btn-link">Xem thêm</button>
        </div>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<style>
</style>

<script>
// Hiển thị modal khi nhấn nút bộ lọc
document.querySelector('.filter-toggle').addEventListener('click', function() {
  var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
  filterModal.show();
});

// Áp dụng bộ lọc từ modal
document.querySelector('.apply-filter').addEventListener('click', function() {
  // Lấy các bộ lọc đã chọn từ modal
  var selectedCities = Array.from(document.querySelectorAll('#filterModal .cities .filter-item.active'))
    .map(item => item.dataset.city);
  var selectedTags = Array.from(document.querySelectorAll('#filterModal .tags .filter-item.active'))
    .map(item => item.dataset.tag);

  // Cập nhật URL hoặc gửi request với các bộ lọc
  var url = new URL(window.location);
  if (selectedCities.length) {
    url.searchParams.set('city', selectedCities.join(','));
  } else {
    url.searchParams.delete('city');
  }
  if (selectedTags.length) {
    url.searchParams.set('tags', selectedTags.join(','));
  } else {
    url.searchParams.delete('tags');
  }
  window.location = url;
});

// Xử lý sự kiện chọn bộ lọc
document.querySelectorAll('.filter-item').forEach(item => {
  item.addEventListener('click', function(e) {
    e.preventDefault();
    this.classList.toggle('active');
  });
});

// Xử lý nút xóa
document.querySelectorAll('.filter-clear').forEach(button => {
  button.addEventListener('click', function() {
    var filterType = this.dataset.filter;
    var url = new URL(window.location);
    url.searchParams.delete(filterType);
    window.location = url;
  });
});
</script>
<div class="col-md-3">
  <div class="col_left">
    <!-- Bộ lọc Thành phố -->
    <div class="filter-section">
      <div class="filter-header">
        <h6 class="filter-title">Thành phố</h6>
        <button class="filter-clear" data-filter="city">Xóa</button>
      </div>
      <div class="cities">
        <!-- Ví dụ dữ liệu tĩnh -->
        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="city" id="city_1" value="1">
          <label class="form-check-label" for="city_1">Hà Nội</label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="city" id="city_2" value="2">
          <label class="form-check-label" for="city_2">TP. Hồ Chí Minh</label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="city" id="city_3" value="3">
          <label class="form-check-label" for="city_3">Đà Nẵng</label>
        </div>
      </div>
      <button id="load-more-cities" class="btn btn-link">Xem thêm</button>
    </div>

    <!-- Bộ lọc Tiện nghi và Dịch vụ đi kèm (Tags) -->
    <div class="filter-section">
      <div class="filter-header">
        <h6 class="filter-title">Tiện nghi và dịch vụ</h6>
        <button class="filter-clear" data-filter="tags">Xóa</button>
      </div>
      <div class="tags">
        <!-- Ví dụ dữ liệu tĩnh -->
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="tags[]" id="tag_1" value="1">
          <label class="form-check-label" for="tag_1">Wi-Fi miễn phí</label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="tags[]" id="tag_2" value="2">
          <label class="form-check-label" for="tag_2">Bể bơi</label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="tags[]" id="tag_3" value="3">
          <label class="form-check-label" for="tag_3">Phòng gym</label>
        </div>
      </div>
      <button id="load-more-tags" class="btn btn-link">Xem thêm</button>
    </div>
  </div>
</div>
<style>
/* Kiểu cho section bộ lọc */
.filter-section {
  background-color: var(--white-color);
  padding: 1rem;
  border: 1px solid var(--boder-color);
}

/* Tiêu đề và nút xóa */
.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-title {
  color: var(--text-color);
  font-weight: 600;
  margin-bottom: 1rem;
}

.filter-clear {
  display: none;
  margin-bottom: 1rem;
  background: none;
  border: none;
  color: var(--primary-color);
  font-size: 16px;
  cursor: pointer;
  padding: 0;
}

.filter-clear:hover {
  text-decoration: underline;
}

.filter-clear.visible {
  display: block;
}

/* Kiểu cho radio và checkbox */
.form-check {
  display: flex;
  align-items: center;
}

.form-check-input {
  margin-right: 8px;
  margin-bottom: 5px;
}

.form-check-input:checked {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
}

.form-check-label {
  color: var(--text-color);
  font-size: 16px;
}

/* Nút Xem thêm */
.btn.btn-link {
  padding: 0;
  color: var(--primary-color);
  text-decoration: none;
}

.btn.btn-link:hover {
  text-decoration: underline;
}
</style>
<div class="col-md-3">
  <div class="col_left">
    <!-- Bộ lọc Thành phố -->
    <div class="filter-section">
      <div class="filter-header">
        <h6 class="filter-title">Thành phố</h6>
        <button class="filter-clear" data-filter="city">Xóa</button>
      </div>
      <div class="cities">
      </div>
    </div>

    <!-- Bộ lọc Tiện nghi và Dịch vụ đi kèm (Tags) -->
    <div class="filter-section">
      <div class="filter-header">
        <h6 class="filter-title">Tiện ích</h6>
      </div>
      <div class="tags">
      </div>
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
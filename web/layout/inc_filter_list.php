<?php
// File: inc_filter_list.php
?>

<div class="filter-container" style='width: 25%;'>
    <!-- Nút Bộ lọc hiển thị trên mobile -->
    <button class="filter-btn-mobile d-md-none btn btn-custom-filter mb-3" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="fas fa-filter me-2"></i> Bộ lọc
    </button>

    <!-- Bộ lọc mặc định hiển thị trên desktop -->
    <div class="filter-content d-none d-md-block">
        <div class="filter-section">
          <div class="filter-header" style='display: flex; justify-content: space-between;'>
            <h6 class="filter-title">Thành phố</h6>
            <div class="filter-clear" data-filter="city">Xóa</div>
        </div>
            <div class="filter-options">
                <?php foreach ($cities as $city): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="city" id="city_<?= $city['city_id'] ?>" value="<?= $city['city_id'] ?>" <?= $id == $city['city_id'] ? 'checked' : '' ?> onchange="window.location.href='<?= $city['city_link'] ?>'">
                        <label class="form-check-label" for="city_<?= $city['city_id'] ?>"><?= htmlspecialchars($city['city_name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="filter-section">
            <h5>Tiện ích</h5>
            <div class="filter-options">
                <?php foreach ($amenities as $amenity): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tags[]" id="tag_<?= $amenity['tag_id'] ?>" value="<?= $amenity['tag_id'] ?>" <?= in_array($amenity['tag_id'], $selected_tags) ? 'checked' : '' ?> onchange="applyFilter()">
                        <label class="form-check-label" for="tag_<?= $amenity['tag_id'] ?>">
                            <?= htmlspecialchars($amenity['tag_name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal Bộ lọc cho mobile -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Bộ lọc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Phần Thành phố trong modal -->
        <div class="filter-section">
          <div class="filter-header" style='display: flex; justify-content: space-between;'>
            <h6 class="filter-title">Thành phố</h6>
            <div class="filter-clear" data-filter="city">Xóa</div>
        </div>
                        <div class="filter-options">
                            <?php foreach ($cities as $city): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="city_modal" id="city_modal_<?= $city['city_id'] ?>" value="<?= $city['city_id'] ?>" <?= $id == $city['city_id'] ? 'checked' : '' ?> onchange="applyFilterModal()">
                                    <label class="form-check-label" for="city_modal_<?= $city['city_id'] ?>"><?= htmlspecialchars($city['city_name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Phần Tiện ích trong modal -->
                    <div class="filter-section">
                        <h6>Tiện ích</h6>
                        <div class="filter-options">
                            <?php foreach ($amenities as $amenity): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tags_modal[]" id="tag_modal_<?= $amenity['tag_id'] ?>" value="<?= $amenity['tag_id'] ?>" <?= in_array($amenity['tag_id'], $selected_tags) ? 'checked' : '' ?> onchange="applyFilterModal()">
                                    <label class="form-check-label" for="tag_modal_<?= $amenity['tag_id'] ?>">
                                        <?= htmlspecialchars($amenity['tag_name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
  /* Ipad */
  @media (min-width: 768px) and (max-width: 1024px) {
    .hotel-list-search .baler-box {
      top: -10%;
    }

    .list_hotel { 
        margin-top: 50% !important;
    }
  }

  /* moblie */
    @media (max-width: 768px) {
        .filter-container {
          width: 100% !important;
          
        }
        .filter-btn-mobile {
            display: block !important;
        }
        .list_hotel {
            flex-direction: column !important;
            margin-top: 70% !important;
        }

        .hotel-list-search .baler-box { 
            top: 11% !important;
        }
        .daterangepicker { 
            top: 130% !important;
        }
    }

    /* Nút Bộ lọc trên mobile */
    .filter-btn-mobile {
        width: 100%;
        padding: 10px 15px;
        font-size: 16px;
        background-color: var(--secondary-color); 
        color: white;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .filter-clear {
        cursor: pointer;
    }
    .filter-btn-mobile:hover {
        background-color: var(--primary-color);
        color: #fff !important;
    }

    .filter-btn-mobile i {
        margin-right: 8px;
    }

    .modal-content {
        border-radius: 12px;
        width: 107%;
    }

    .modal-header {
        border-bottom: none;
        padding: 15px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
    }

    .modal-body {
        padding: 15px;
    }

    .filter-section {
        margin-bottom: 20px;
    }

    .form-check-label {
        margin-left: 8px;
    }

</style>

<script>
function applyFilter() {
    const tags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked')).map(input => input.value);
    const city = document.querySelector('input[name="city"]:checked')?.value || '';
    let url = '?';
    if (city) url += `city=${city}`;
    if (tags.length > 0) url += `${city ? '&' : ''}tags=${tags.join(',')}`;
    window.location.href = url;
}

function applyFilterModal() {
    const tags = Array.from(document.querySelectorAll('input[name="tags_modal[]"]:checked')).map(input => input.value);
    const city = document.querySelector('input[name="city_modal"]:checked')?.value || '';
    let url = '?';
    if (city) url += `city=${city}`;
    if (tags.length > 0) url += `${city ? '&' : ''}tags=${tags.join(',')}`;
    window.location.href = url;
}
</script>
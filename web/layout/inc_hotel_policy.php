<div class="hotel-policy shadow-sm border-0 mb-4">
    <h5 class="title-section" id="box_hotel_policy">Chính sách tại <?= $hotel_detail['hot_name'] ?></h5>
    <div class="policy-list">
    <div class="checkin_checkout d-flex">
        <!-- Nhận phòng -->
        <div class="checkin-section pe-3 me-3 checkin_section_border">
            <div class="checkin-label"><i class="fas fa-sign-in-alt"></i> Nhận phòng</div>
            <div class="checkin-time">Từ <?= $hotel_detail['hot_checkin'] ?></div>
        </div>

        <!-- Trả phòng -->
        <div class="checkout-section">
            <div class="checkout-label"><i class="fas fa-sign-out-alt"></i> Trả phòng</div>
            <div class="checkout-time">Trước <?= $hotel_detail['hot_checkout'] ?></div>
        </div>
    </div>
        <!-- Chính sách chung -->
        <div class="policy-item long-policy">
            <div class="policy-label" data-bs-toggle="collapse" data-bs-target="#generalPolicy" aria-expanded="false"
                aria-controls="generalPolicy">
                <i class="fas fa-file-alt"></i>Chính sách chung
            </div>
            <div class="collapse policy-value" id="generalPolicy">
                <?= $hotel_detail['hot_policy_general'] ?>
            </div>
        </div>

        <!-- Chính sách hủy -->
        <div class="policy-item long-policy">
            <div class="policy-label" data-bs-toggle="collapse" data-bs-target="#cancelPolicy" aria-expanded="false"
                aria-controls="cancelPolicy">
                <i class="fas fa-ban"></i>Chính sách hủy
            </div>
            <div class="collapse policy-value" id="cancelPolicy">
                <?= $hotel_detail['hot_policy_cancel'] ?>
            </div>
        </div>
    </div>
</div>


<style>
    .checkin_section_border{
        border-right: 1px solid #e2e8f0;
    }
    .hotel-policy {
        background-color: var(--white);
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .checkin_checkout {
        position: relative;
        margin: 0 20px 20px 20px;
    }
    
    .checkin-label, 
    .checkout-label {
        font-weight: 600;
        font-size: 16px;
        color: #4a5568;
    }
    
    .checkin-time,
    .checkout-time {
        font-size: 14px;
        color: #718096;
    }
    .policy-value li{
        margin-left: 20px;
    }
    .hotel-policy .title-section {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .policy-item {
        margin-bottom: 1rem;
    }

    .policy-item .policy-label {
        color: var(--text-color);
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 0.5rem;
        transition: background-color 0.3s ease;
    }

    .policy-item .policy-label:hover {
        background-color: var(--accent-color-hover);
        border-radius: 0.375rem;
    }

    .policy-item .policy-label i {
        margin-right: 0.5rem;
        transition: transform 0.3s ease;
    }

    .policy-item .policy-label i.rotate {
        transform: rotate(180deg);
    }

    .policy-item .policy-value {
        color: var(--text-color);
        padding: 1rem;
        background-color: var(--background-color);
        border-radius: 0.375rem;
    }

    .policy-item .policy-value p {
        margin-bottom: 0.5rem;
    }

    .policy-item .policy-value ul {
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .policy-item .policy-value ul li {
        margin-bottom: 0.25rem;
    }

    .policy-item.short-policy .policy-value {
        background-color: transparent;
        padding: 0.5rem 0;
    }

    @media (max-width: 576px) {
        .hotel-policy {
            padding: 1.5rem;
        }

        .hotel-policy .title-section {
            font-size: 1.25rem;
        }

        .policy-item .policy-label {
            font-size: 0.9375rem;
        }

        .policy-item .policy-value {
            font-size: 0.875rem;
        }
    }
</style>
<script>
    function initHotelPolicyJS() {
        try {
            $('.long-policy .policy-label').on('click', function () {
                const $icon = $(this).find('i');
                $icon.toggleClass('rotate');
            });

            // Đồng bộ icon khi collapse thay đổi
            $('.long-policy .collapse').on('show.bs.collapse', function () {
                $(this).prev('.policy-label').find('i').addClass('rotate');
            });
            $('.long-policy .collapse').on('hide.bs.collapse', function () {
                $(this).prev('.policy-label').find('i').removeClass('rotate');
            });
        } catch (error) {
            console.error('Error in hotel_policy.js:', error);
        }
    }

    // Kiểm tra jQuery đã load chưa
    if (window.jQuery) {
        $(document).ready(initHotelPolicyJS);
    } else {
        const waitForJQuery = setInterval(() => {
            if (window.jQuery) {
                clearInterval(waitForJQuery);
                $(document).ready(initHotelPolicyJS);
            }
        }, 100);
    }
</script>
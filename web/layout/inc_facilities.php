<div class="facilities-section">
    <h5 class="title-section" id="box_hotel_attribute">Tiện ích</h5>
    <div class="facilities-list">
        <?php foreach ($hotel_attribute as $row): ?>
            <div class="facility-group">
                <h3 class="group-title"><?php echo $row['info']['name']; ?></h3>
                <div class="row">
                    <?php foreach ($row["data"] as $row): ?>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="facility-item">
                                <i class="<?php echo $row['icon']; ?>"></i>
                                <span><?php echo $row["name"]; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .facilities-section {
        background-color: var(--white);
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .facilities-section .title-section {
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .facility-group {
        margin-bottom: 10px;
    }

    .facility-group .group-title {
        color: var(--text-color);
        font-weight: 500;
        font-size: 1.125rem;
        margin-bottom: 1rem;
    }

    .facility-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        color: var(--text-color);
    }

    .facility-item i {
        color: var(--accent-color);
        margin-right: 0.75rem;
        font-size: 14px;
    }

    .facility-item span {
        font-size: 14px;
        color: var(--text-light);
    }

    @media (max-width: 576px) {
        .facilities-section {
            padding: 1rem;
        }

        .facilities-section .title-section {
            font-size: 1.25rem;
        }

        .facility-group .group-title {
            font-size: 1rem;
        }

        .facility-item span {
            font-size: 0.875rem;
        }
    }
</style>
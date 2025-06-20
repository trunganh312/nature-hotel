<div class="intro-section my-4">
    <h5 class="title-section">Giới thiệu về <?= $hotel_detail['hot_name'] ?></h5>
    <div class="intro-content">
        <?= $hotel_detail['hot_description'] ?>
    </div>
</div>
<style>
    .intro-section {
        background-color: var(--white);
        border-radius: 0.5rem;
        padding: 2rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .intro-section .title-section {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .intro-section .intro-content {
        color: var(--text-color);
        font-size: 1rem;
        line-height: 1.6;
    }

    .intro-section .intro-content p {
        margin-bottom: 1rem;
    }

    .intro-section .highlight {
        color: var(--accent-color);
        font-weight: 500;
    }

    .intro-section .icon-list {
        margin-top: 1.5rem;
    }

    .intro-section .icon-list .icon-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .intro-section .icon-list .icon-item i {
        color: var(--accent-color);
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }

    .intro-section .icon-list .icon-item span {
        color: var(--text-light);
        font-size: 0.9375rem;
    }

    @media (max-width: 576px) {
        .intro-section {
            padding: 1.5rem;
        }

        .intro-section .title-section {
            font-size: 1.25rem;
        }

        .intro-section .intro-content {
            font-size: 0.9375rem;
        }

        .intro-section .icon-list .icon-item span {
            font-size: 0.875rem;
        }
    }
</style>
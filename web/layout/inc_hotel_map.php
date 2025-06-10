<div id="map-section" class="map-section mt-4 mb-4">
    <h5 class="title-section">Vị trí trên bản đồ</h5>
    <div class="map-iframe">
        <iframe width="100%" height="450" style="border: 0" loading="lazy" allowfullscreen
            src="https://maps.google.com/maps?q=<?= $hotel['hot_lat'] ?>,<?= $hotel['hot_lon'] ?>&hl=vi&z=14&output=embed">
        </iframe>
    </div>
</div>

<style>
    .map-section {
        background-color: var(--white);
        border-radius: 0.5rem;
        padding: 2rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .map-section .title-section {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .map-section .map-iframe {
        width: 100%;
        height: 400px;
        border: 0;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    @media (max-width: 576px) {
        .map-section {
            padding: 1.5rem;
        }

        .map-section .title-section {
            font-size: 1.25rem;
        }

        .map-section .map-iframe {
            height: 300px;
        }
    }
</style>
<div id="map-section" class="map-section mt-4">
    <h5 class="title-section">Vị trí trên bản đồ</h5>
    <div class="map-iframe">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30697.649823589472!2d108.30572455689075!3d15.898263198686019!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31420f0b279e9271%3A0x9dfd665fdfc63e4e!2sHoi%20An%20Town%20Home%20Resort!5e0!3m2!1svi!2s!4v1732959599035!5m2!1svi!2s"
            width="100%" height="400" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
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
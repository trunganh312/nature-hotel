<?php
// news-area.php
?>
<section class="news-area py-5" style="background-color: var(--background-color);">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-color);">Tin Tức Mới</h2>
            <div class="section-title-line"></div>
            <p class="text-muted mt-2" style="color: var(--text-light);">Cập nhật những thông tin và sự kiện mới nhất từ Nature Hotel</p>
        </div>
        <div class="row gy-4">
            <?php foreach ($news_list as $news): ?>
            <div class="col-xl-4 col-lg-4 col-md-6" >
                <div class="news-card bg-white rounded-3 shadow-sm overflow-hidden" data-news style='max-height: 300px; min-height: 300px;'>
                    <a href="<?= htmlspecialchars($news['link']) ?>" class="news-card-img position-relative">
                        <img src="<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="img-fluid w-100" style=" object-fit:cover; max-height: 200px; min-height: 200px;">
                        <div class="news-overlay"></div>
                        <i class="fas fa-newspaper news-icon position-absolute"></i>
                    </a>
                    <div class="news-card-content p-2">
                        <h4 class="news-title mb-0">
                            <a href="<?= htmlspecialchars($news['link']) ?>" style="color: var(--text-color);"><?= htmlspecialchars($news['title']) ?></a>
                        </h4>
                        <div class="text-muted small mt-1"><?= htmlspecialchars($news['created']) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
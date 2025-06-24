<?php if (isset($document) && !empty($document)): ?>

<main class="document-detail row">
    <div class="col-md-8">
        <h1><?= htmlspecialchars($document['doc_name']) ?></h1>
        <div class="meta" style='text-align: right;'>
            <span>Ngày đăng: <?= date('d/m/Y', strtotime($document['created_at'])) ?></span>
        </div>
        <div class="content">
            <?= htmlspecialchars_decode($document['doc_content']) ?>
        </div>
    </div>
    <aside class="col-md-4">
        <div style='background: var(--primary-color ); padding: 12px; border-radius: 10px; margin-bottom: 24px;'>
            <h3>
            Bài viết liên quan
        </h3>
        </div>
        <ul class="list-unstyled">
            <?php foreach ($other_documents as $item): ?>
                <li>
                    <a href="/document/<?= htmlspecialchars($item['doc_slug']) ?>-<?= $item['doc_id'] ?>.html" title="<?= htmlspecialchars($item['doc_name']) ?>" style="display: flex; align-items: center; gap: 14px; text-decoration: none;">
                        <div class="thumb">
                            <img src="<?= !empty($item['doc_thumb']) ? htmlspecialchars($item['doc_thumb']) : '/assets/img/no-image.png' ?>" alt="<?= htmlspecialchars($item['doc_name']) ?>">
                        </div>
                        <div class="info">
                            <?= htmlspecialchars($item['doc_name']) ?>
                            <div class="small text-muted"><?= date('d/m/Y', strtotime($item['created_at'])) ?></div>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
</main>
<?php else: ?>
<main class="document-detail">
    <h1>Bài viết không tồn tại</h1>
    <p>Xin lỗi, chúng tôi không tìm thấy bài viết bạn yêu cầu.</p>
    <a href="/">Quay lại trang chủ</a>
</main>
<?php endif; ?>
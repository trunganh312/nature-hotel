<div class="container mt-5">
    <h1 class="mb-4 text-center fw-bold fs-1" style='color: var(--primary-color);' >Tin tức</h1>
    <div class="row">
        <?php foreach ($news as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100" style='height: 300px;'>
                    <a href="/document/<?= htmlspecialchars($item['doc_slug']) ?>-<?= $item['doc_id'] ?>.html">
                        <img src="<?= $item['doc_img'] ? htmlspecialchars($Router->srcDocument($item['doc_img'])) : '/assets/img/no-image.png' ?>" class="card-img-top" alt="<?= htmlspecialchars($item['doc_name']) ?>" style='max-height: 180px; min-height: 180px;'>
                    </a>
                    <div class="card-body p-3 ">
                        <h5 class="card-title">
                            <a href="/document/<?= htmlspecialchars($item['doc_slug']) ?>-<?= $item['doc_id'] ?>.html"><?= htmlspecialchars($item['doc_name']) ?></a>
                        </h5>
                        <div class="text-muted small"><?= date('d/m/Y', strtotime($item['created_at'])) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Phân trang -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item<?= $i == $page ? ' active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
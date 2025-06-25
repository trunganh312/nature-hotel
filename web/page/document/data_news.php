<?php

use src\Models\Document; 
use src\Facades\DB;

include_once($path_root . 'Core/Config/require_web.php');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

$total = Document::where('doc_active', 1)->count();

$news = DB::pass()->query("
    SELECT doc_id, doc_name, doc_img, doc_slug, created_at
    FROM document
    WHERE doc_active = 1
    ORDER BY created_at DESC
    LIMIT $offset, $per_page
")->toArray();

$total_pages = ceil($total / $per_page);
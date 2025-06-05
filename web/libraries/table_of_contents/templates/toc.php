<div id="toc_main" class="toc_list_main">
    <div class="toc_head">
        <i class="fal fa-book"></i>
        <p class="toc_title_main">Mục lục</p>
        <i class="fal fa-chevron-down"></i>
    </div>
    <div class="toc_body">
    <?php foreach($dataToc as $item): ?>
        <?php if ($item['list_open']): ?>
            <ul class="toc_list_main">
        <?php endif ?>
            <?php if ($item['item_open']): ?>
                <li>
            <?php endif ?>
                <?php if ($item['text']): ?>
                    <a href="<?= $item['href'] ?>" class="st-link event_article_table_content" title="<?= $item['text'] ?>"><?= $item['text'] ?></a>
                <?php endif ?>
            <?php if ($item['item_close']): ?>
                </li>
            <?php endif ?>
        <?php if ($item['list_close']): ?>
            </ul>
        <?php endif ?>
    <?php endforeach; ?>
    </div>
</div>

<div id="toc_sub">                                
    <div id="trigger">
        <i class="fal fa-chevron-right"></i>
    </div>
    <div id="wrapper">
        <div class="toc_head">
            <i class="fal fa-list-ol"></i> Mục lục
        </div>
        <div class="toc_body">
            <?php foreach($dataToc as $item): ?>
                <?php if ($item['list_open']): ?>
                    <ul class="toc_list_sub">          
                <?php endif ?>
                    <?php if ($item['item_open']): ?>
                        <li>
                    <?php endif ?>
                        <?php if ($item['text']): ?>
                            <a href="<?= $item['href'] ?>" class="st-link event_article_table_content" title="<?= $item['text'] ?>"><?= $item['text'] ?></a>
                        <?php endif ?>
                    <?php if ($item['item_close']): ?>
                        </li>
                    <?php endif ?>
                <?php if ($item['list_close']): ?>
                    </ul>
                <?php endif ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>



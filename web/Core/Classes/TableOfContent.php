<?php 
/**
 * Xử lý khởi tạo mục lục
 */

class TableOfContent {

    private $data = [];
    private $last_data = [];

    /**
     * Thêm một phần tử vào mục lục
     * $index_of_parent tồn tại thì phần tử sẽ được coi là phần tử con của phần tử theo index truyền vào
     */
    public function append($selector, $label, $index_of_parent=-1) {
        $item = compact('selector' ,'label');
        if(isset($this->data[$index_of_parent])) {
            $this->data[$index_of_parent]["subs"][] = $item;
            return;
        }
        $item['subs'] = [];
        $this->data[] = $item;
    }

    /**
     * Thêm một phần tử vào mục lục khi tất cả các func append đã chạy xong
     */
    public function lastAppend($selector, $label) {
        $item = compact('selector' ,'label');
        $item['subs'] = [];
        $this->last_data[] = $item;
    }

    /**
     * Trả về index của phần tử cuối cùng trong mục lục
     */
    public function lastIndex() {
       return empty($this->data) ? 0 : count($this->data) - 1;
    }

    /**
     * Gộp biến last_data vào biến data để bắt đầu render html
     */
    private function mergeData() {
        foreach($this->last_data as $row) {
            $this->data[] = $row;
        }
    }

    /**
     * Sử dụng để debug data trong mục lục
     */
    public function dump() {
        $this->mergeData();
        dump($this->data);
    }

    /**
     * Sử dụng để tạo mã html từ data mục lục
     */
    public function render($export=false) {
        $this->mergeData();
        $html_items = [];
        foreach($this->data as $index => $row) {
            $stt = $index+1;
            $html_items[] = "<li><a class=\"st-link event_product_table_content\" href=\"{$row['selector']}\"><span>{$stt}</span>. {$row['label']}</a></li>";
            foreach($row['subs'] as $index => $row) {
                $index++;
                $html_items[] = "<li><a class=\"st-link sub_link event_product_table_content\" href=\"{$row['selector']}\"><span>{$stt}.{$index}</span>. {$row['label']}</a></li>";
            }
        }
        $html = '
            <div id="toc_sub">
                <div id="trigger">
                    <i class="fal fa-chevron-right"></i>
                </div>
                <div id="wrapper">
                    <div class="toc_head">
                        <i class="fal fa-list-ol"></i> Mục lục
                    </div>
                    <div class="toc_body">
                        <ul class="toc_list_sub">
                        '. implode('', $html_items) .'
                        </ul>
                    </div>
                </div>
            </div>';
        if($export) {
            return $html;
        }
        echo $html;
    }

    /**
     * Sử dụng để tạo mã html từ data mục lục mẫu 2
     */
    public function render2() {
        global $path_root;

        $this->mergeData();
        $dataToc = [];
        $dataToc[] = [
            "item_open"=> false,
            "item_close"=> false,
            "list_open"=> true,
            "list_close"=> false,
            "text"=> '',
            "href"=> ''
        ];

        foreach($this->data as $index => $row) {
            $stt = $index+1;
            if (!empty($row['subs'])) {
                $dataToc[] = [
                    "item_open"=> true,
                    "item_close"=> false,
                    "list_open"=> false,
                    "list_close"=> false,
                    "text"=> '',
                    "href"=> ''
                ];
            }
            $dataToc[] = [
                "item_open"=> true,
                "item_close"=> true,
                "list_open"=> false,
                "list_close"=> false,
                "text"=> $stt .'. '. $row['label'],
                "href"=> $row['selector']
            ];
            $dataToc[] = [
                "item_open"=> false,
                "item_close"=> false,
                "list_open"=> !empty($row['subs']),
                "list_close"=> false,
                "text"=> '',
                "href"=> ''
            ];
            foreach($row['subs'] as $index => $row_chlid) {
                $index++;
                $dataToc[] = [
                    "item_open"=> true,
                    "item_close"=> true,
                    "list_open"=> false,
                    "list_close"=> false,
                    "text"=> $stt .'.'. $index .'. '. $row_chlid['label'],
                    "href"=> $row_chlid['selector']
                ];
            }
            $dataToc[] = [
                "item_open"=> false,
                "item_close"=> false,
                "list_open"=> false,
                "list_close"=> !empty($row['subs']),
                "text"=> '',
                "href"=> ''
            ];
            if (!empty($row['subs'])) {
                $dataToc[] = [
                    "item_open"=> false,
                    "item_close"=> true,
                    "list_open"=> false,
                    "list_close"=> false,
                    "text"=> '',
                    "href"=> ''
                ];
            }
        }
        $dataToc[] = [
            "item_open"=> false,
            "item_close"=> false,
            "list_open"=> false,
            "list_close"=> true,
            "text"=> '',
            "href"=> ''
        ];
        require($path_root .'libraries/table_of_contents/templates/toc.php');
    }
}
<?

use src\Models\Hotel;

include('Core/Config/require_web.php');

$Layout->setTitle($cfg_website['con_meta_title'])
        ->setDescription($cfg_website['con_meta_description'])
        ->setKeywords($cfg_website['con_meta_keyword'])
        ->setImages(['src' => $cfg_default_image, 'alt' => $cfg_website['con_meta_title']])
        ->setCanonical(DOMAIN_WEB)
        ->setJS(['page.home']);

// Danh sách các điểm đến 
$hotels = Hotel::where('hot_active', 1)
                ->select('cit_name', 'cit_id')
                ->join('cities', 'hot_city', 'cit_id')
                ->toArray();

$data_city_count = [];
foreach ($hotels as $hotel) {
        $name = $hotel['cit_name'];
        if (!isset($data_city_count[$name])) {
                $data_city_count[$name] = 0;
        }
        $data_city_count[$name]++;
}

$data_city = [];
foreach ($data_city_count as $name => $value) {
        $slug = to_slug($hotel['cit_name']);
        $data_city[] = ['name' => $name, 'value' => $value, 'link' => '/city-' . $hotel['cit_id'] . '-' . $slug . '.html'];
}


?>
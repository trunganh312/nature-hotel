<?

use src\Models\Hotel;

include('Core/Config/require_web.php');

$Layout->setTitle($cfg_website['con_meta_title'])
        ->setDescription($cfg_website['con_meta_description'])
        ->setKeywords($cfg_website['con_meta_keyword'])
        ->setImages(['src' => $cfg_default_image, 'alt' => $cfg_website['con_meta_title']])
        ->setCanonical(DOMAIN_WEB)
        ->setJS(['page.home']);

$hotels = Hotel::where('hot_active', 1)
    ->select('cit_name', 'cit_id', 'cit_image')
    ->join('cities', 'hot_city', 'cit_id')
    ->toArray();

$data_city = [];
foreach ($hotels as $hotel) {
    $name = $hotel['cit_name'];
    if (!isset($data_city[$name])) {
        $slug = to_slug($name);
        $data_city[$name] = [
            'name' => $name,
            'value' => 1,
            'link' => '/city-' . $hotel['cit_id'] . '-' . $slug . '.html',
            'img' => $hotel['cit_image']
        ];
    } else {
        $data_city[$name]['value']++;
    }
}
$data_city = array_values($data_city);

?>
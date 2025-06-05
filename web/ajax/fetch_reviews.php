<?
include('../Core/Config/require_web.php');

$inc_reviews_data = [
    'group' =>  getValue('group'),
    'item'  =>  getValue('id')
];

include($path_root . 'layout/inc_reviews.php');
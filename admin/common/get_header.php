<?php

use src\Facades\DB;
use src\Libs\Utils;
use src\Models\User;
use src\Services\CommonService;

include('config_module.php');


// Lấy DS tài khoản chưa được active
$users = User::where('use_active', 0)->toArray();

$res = [
    'data_user' => [
        'title' => 'Tài khoản user chưa được active',
        'total' => count($users),
        'link'  => '/account/user_list.php?active=false',
        'show'  => true
    ],
    'total' => count($users)
];

CommonService::resJson($res);

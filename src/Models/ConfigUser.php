<?php

namespace src\Models;

use src\Facades\Model;

class ConfigUser extends Model {

    static protected $table = 'config_user';
    static protected $company_key = 'cous_company_id';
}
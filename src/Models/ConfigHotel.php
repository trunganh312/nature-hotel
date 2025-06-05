<?php

namespace src\Models;

use src\Facades\Model;

class ConfigHotel extends Model {

    static protected $table = 'config_hotel';
    static protected $company_key = 'coh_company_id';
}
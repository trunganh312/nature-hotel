<?php

namespace src\Models;

use src\Facades\Model;

class Hotel extends Model {

    static protected $table = 'hotel';
    static protected $company_key = 'hot_company_id';

}
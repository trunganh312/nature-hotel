<?php

namespace src\Models;

use src\Facades\Model;

class CustomerCorporate extends Model {

    static protected $table = 'customer_corporate';
    static protected $company_key = 'copc_company_id';
    static protected $hotel_field = 'copc_hotel_id';

}
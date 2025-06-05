<?php

namespace src\Models;

use src\Facades\Model;

class Customer extends Model {

    static protected $table = 'customer';
    static protected $company_key = 'cus_company_id';
    static protected $hotel_field = 'cus_hotel_id';

}
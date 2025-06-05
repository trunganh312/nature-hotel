<?php

namespace src\Models;

use src\Facades\Model;

class CustomerRequest extends Model {

    static protected $table = 'customer_request';
    static protected $company_key = 'cure_company_id';
    static protected $hotel_field = 'cure_hotel_id';

}
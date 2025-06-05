<?php

namespace src\Models;

use src\Facades\Model;

class HotelBank extends Model {

    static protected $table = 'hotel_bank';
    static protected $company_key = 'hoba_company_id';
    static protected $hotel_field = 'hoba_hotel_id';

}
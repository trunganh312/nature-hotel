<?php

namespace src\Models;

use src\Facades\Model;

class BookingHotel extends Model {

    const CUSTOMER_INDIVIDUAL = 0; 
    const CUSTOMER_GROUP = 1; 

    static protected $table = 'booking_hotel';
    static protected $company_key = 'bkho_company_id';
    static protected $hotel_field = 'bkho_hotel_id';

}
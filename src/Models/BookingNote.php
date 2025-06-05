<?php

namespace src\Models;

use src\Facades\Model;

class BookingNote extends Model {

    static protected $table = 'booking_note';
    static protected $company_key = 'bkno_company_id';
}
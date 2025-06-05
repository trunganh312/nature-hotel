<?php

namespace src\Models;

use src\Facades\Model;

class MoneyBooking extends Model {

    static protected $table = 'money_booking';
    static protected $company_key = 'mobk_company_id';
    
}
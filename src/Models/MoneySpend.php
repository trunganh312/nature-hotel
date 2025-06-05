<?php

namespace src\Models;

use src\Facades\Model;

class MoneySpend extends Model {

    static protected $table = 'money_spend';
    static protected $company_key = 'mosp_company_id';
}
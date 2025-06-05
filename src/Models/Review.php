<?php

namespace src\Models;

use src\Facades\Model;

class Review extends Model {

    static protected $table = 'reviews';
    static protected $company_key = 'rev_company_id';

}
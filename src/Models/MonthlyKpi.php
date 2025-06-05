<?php

namespace src\Models;

use src\Facades\Model;

class MonthlyKpi extends Model {

    static protected $table = 'monthly_kpi';
    static protected $company_key = 'mokp_company_id';
}
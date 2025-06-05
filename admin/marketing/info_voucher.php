<?
include('config_module.php');

$code   = getValue('code', GET_STRING, GET_GET, '');
$money  = getValue('money', GET_DOUBLE);
$discount_money = $VoucherModel->getMoneyDiscountByCode($code, $money);

if ($VoucherModel->error_code) {
    exit(json_encode([
        'error'=> $VoucherModel->error_code
    ]));
}

$money_pay = $money - $discount_money;
$money_pay = $money_pay < 0 ? 0 : $money_pay;
exit(json_encode(compact('code', 'money', 'discount_money', 'money_pay')));
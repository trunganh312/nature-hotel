<?
include('config_module.php');

if (!$Auth->cto) {
    redirect_url('/');
}

$Auth->fakeLogin();
?>
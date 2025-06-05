<?
/**
 * Khai báo các thông số cấu hình cho environment
 */

/** --- Environment --- **/
define('ENV_ENVIRONMENT', 'dev');   //dev, pro

/** --- Database --- **/
define('ENV_DB_HOST', '10.8.0.1');
define('ENV_DB_USERNAME', 'adm_hieunq');
define('ENV_DB_PASSWORD', '9IMYyn8zQBT2)aHR');
define('ENV_DB_DBNAME', 'db_sennet');
define('ENV_DB_LOG_DBNAME', 'db_sennet');
define('ENV_DB_SLOW_QUERY', 0.1);

/** --- Domain --- **/
define('DOMAIN_CRM', 'http://crm.sennet.local');    //Ko bao gồm / ở cuối
define('DOMAIN_USER', 'http://sennet.local');    //Ko bao gồm / ở cuối
define('DOMAIN_STATIC', 'http://static.sennet.local');    //Ko bao gồm / ở cuối
define('WEBSITE_NAME', 'SENNET');

/** --- Email dùng cho class Mailer --- **/
define('MAILER_USERNAME', '');  //Account đăng nhập vào Email dùng để gửi đi
define('MAILER_PASSWORD', '');  //Password của Email dùng để gửi đi
define('MAILER_DEV_TEST', '');  //Email nhận ở môi trường test

// Lấy license tại đây https://ckfinder.sanvu88.net/
// Version 3
define('CKEDITOR_USER', 'cms.vietgoing.com');
define('CKEDITOR_PASSWORD', '25U59PPPU8GMXV9ULEMN7TBTSE6FM');

?>
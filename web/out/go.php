<?
$go =   'https://vietgoing.com';
if (isset($_GET['url']) && $_GET['url'] != '') {
   $go   =  base64_decode($_GET['url']);
}
echo '<meta http-equiv="refresh" content="0;url=' . $go . '">';
exit;
//header("Location: " . $go);
?>
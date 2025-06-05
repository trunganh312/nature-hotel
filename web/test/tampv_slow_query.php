<?php
// include('../Core/Config/require_web.php');

// class DBTest extends Database {
//     /** Path save log **/
//     private $path_log;

//     /** Connector **/
//     private $con;
    
//     private $connected  =   false;
//     /** Show debug query **/
//     private $debug_query = false;

//     private $db_log      = null;
    
//     private $max_connection =   100;
    
//     private $count_con  =   1;
    
//     private $result;

//     private function saveLog($file, $content, $file_line = '')
//     {

//         $endline = "\n";
//         //if($_SERVER['SERVER_NAME'] == "localhost")   $endline =  PHP_EOL;
//         $break_line = "---------------------------------------------------------------------------";
//         //Ten file
//         $filename   =   $this->path_log . $file . ".cfn";
        
//         //Mở file để ghi
//         $handle =   fopen($filename, "a");
        
//         //Neu ko mo duoc file thi exit
//         if (!$handle) exit('Error create log!');
        
//         if ($file_line == '') {
            
//             $file_line  =   'File: ' . $this->GetIncludedFile();
            
//         }
        
//         //Noi dung luu log
//         $string =   date("d/m/Y H:i:s") . ' ' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] . $endline;
//         $string .=  "IP:" . @$_SERVER['REMOTE_ADDR'] . $endline;
//         $string .=  $file_line . $endline;
//         $string .=  $endline . $content . $break_line . $endline;
        
//         fwrite($handle, $string);
//         fclose($handle);
//     }

//     function query($query)
//     {
//         $this->sql  =   $query;

//         //Check slow
//         $start = $this->getTime();
        
//         /** Connect DB **/
//         $this->connectDB();
        
//         //Query
//         $result = @mysqli_query($this->con, $query);
        
//         //If fail
//         if (!$result)
//         {
//             /** ======= Goi file & line thuc thi ====== **/
//             $file_line  =   'File: ' . $this->GetIncludedFile();
            
//             $error   = @mysqli_error($this->con) . "\n\n" . $query . "\n\n";
            
//             //Save log error
//             $this->saveLog('query_error', $error, $file_line);
            
//             //Dump loi neu o local hoac test
//             if (is_dev()) {
//                 exit($file_line . ":\n" . $error);
//             }
//         }
//         //Check slow query
//         $finish     =   $this->getTime();
//         $time_query =   $finish - $start;
        
//         /** Nếu query chậm thì lưu log lại để xử lý giảm tải **/
//         if (true)
//         {
//             /** ======= Goi file & line thuc thi ====== **/
//             $file_line  =   'File: ' . $this->GetIncludedFile();

//             $slow   =   $query . "\n\n";
//             $slow   .=  "Query time : " . number_format($time_query, 10, ".", ",") . "\n";
//             $this->saveLog('query_slow', $slow, $file_line);
//         }
        
//         $this->result   =   $result;
        
//         //Return
//         return $this;

//     }
// }

// $DBT = new DBTest;

// $r = $DBT->query("SELECT count(1) FROM hotel LMIT 1")->getOne();

// dd($r);
<?
include('../Core/Config/require_web.php');
exit('Vietgoing.com');
$type   =   getValue('type', GET_STRING, GET_GET, '');

if ($type == 'city') {
    $data   =   $DB->query("SELECT cit_id, cit_name FROM city")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['cit_name'] . ' ' . removeAccent($row['cit_name']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE city SET cit_search_data = '" . $name . "' WHERE cit_id = " . $row['cit_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['cit_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}

if ($type == 'district') {
    $data   =   $DB->query("SELECT dis_id, dis_name FROM district")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['dis_name'] . ' ' . removeAccent($row['dis_name']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE district SET dis_search_data = '" . $name . "' WHERE dis_id = " . $row['dis_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['dis_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}

if ($type == 'ward') {
    $data   =   $DB->query("SELECT war_id, war_name FROM ward")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['war_name'] . ' ' . removeAccent($row['war_name']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE ward SET war_search_data = '" . $name . "' WHERE war_id = " . $row['war_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['war_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}

if ($type == 'destination') {
    $data   =   $DB->query("SELECT des_id, des_name FROM destination")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['des_name'] . ' ' . removeAccent($row['des_name']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE destination SET des_search_data = '" . $name . "' WHERE des_id = " . $row['des_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['des_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}
if ($type == 'hotel') {
    $data   =   $DB->query("SELECT hot_id, hot_name FROM hotel")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['hot_name'] . ' ' . removeAccent($row['hot_name']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE hotel SET hot_data_search = '" . $name . "' WHERE hot_id = " . $row['hot_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['hot_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}

if ($type == 'tour') {
    $data   =   $DB->query("SELECT tou_id, tou_name FROM tour")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['tou_name'] . ' ' . removeAccent($row['tou_name']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE tour SET tou_search_data = '" . $name . "' WHERE tou_id = " . $row['tou_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['tou_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}

if ($type == 'article') {
    $data   =   $DB->query("SELECT art_id, art_title FROM article")->toArray();
    $i  =   0;
    foreach ($data as $row) {
        $name   =   $row['art_title'] . ' ' . removeAccent($row['art_title']);
        $name   =   replaceMQ($name);
        $name   =   mb_strtolower($name, 'UTF-8');
        if ($DB->execute("UPDATE article SET art_search_data = '" . $name . "' WHERE art_id = " . $row['art_id'] . " LIMIT 1") > 0) {
            $i++;
            echo    $row['art_id'] . ': ' . $name . '<br>';
        }    
    }
    echo    '<hr>Total: ' . format_number($i);
}
?>
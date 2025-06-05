<?php
use src\Facades\DB;
use src\Libs\Vue;
use src\Services\CommonService;

include('../../Core/Config/require_crm.php');

// Check permissions
$Auth->checkPermission('application_list');

// Define variables
$table = "application";
$field_id = "app_id";
$page_title = "Danh sách token";
$has_edit = $Auth->hasPermission('application_edit');
$has_create = $Auth->hasPermission('application_create');

// Handle active/inactive toggle
$action = getValue('action', GET_STRING, GET_GET, '');
if ($action === 'toggle_active') {
    $record_id = getValue('id', GET_INT, GET_GET, 0);
    $field = getValue('field', GET_STRING, GET_GET, '');

    // Validate field and permissions
    if ($field !== 'app_active') {
        CommonService::resJson(['error' => 'Invalid field'], STATUS_INACTIVE, 'Error!');
        exit;
    }

    $Auth->checkPermission('application_edit');

    // Get current record
    $record_info = $DB->query("SELECT * FROM $table WHERE $field_id = $record_id")->getOne();
    if (empty($record_info)) {
        CommonService::resJson(['error' => 'Record not found'], STATUS_INACTIVE, 'Error!');
        exit;
    }

    // Toggle value (0 -> 1, 1 -> 0)
    $value = abs($record_info[$field] - 1);

    // Update the field in the database
    if ($DB->execute("UPDATE $table SET $field = $value WHERE $field_id = $record_id LIMIT 1") > 0) {
        CommonService::resJson(['success' => 1]);
        echo generate_checkbox_icon($value);

        // Save log
        $data_new = $DB->query("SELECT $field FROM $table WHERE $field_id = $record_id")->getOne();
        $Log->setTable($table)->genContent($record_info, $data_new)->createLog($record_id);
    } else {
        CommonService::resJson(['error' => 'Update failed'], STATUS_INACTIVE, 'Error!');
    }
    exit;
}

// DataTable setup
$Table = new DataTable($table, $field_id);
$Table->column('app_id', 'ID', TAB_NUMBER, true, false)
    ->column('app_app_id', 'App ID', TAB_TEXT, true, true)
    ->column('app_app_secret', 'App Secret', TAB_TEXT, true, true)
    ->column('app_request', 'Requests', TAB_NUMBER, true, true)
    ->column('app_request_max', 'Max Requests', TAB_NUMBER, true, true);
      

if ($has_edit) {
    $Table->addED(true)
          ->column('app_active', 'Active', TAB_CHECKBOX, true, true)
          ->setEditFileName('application_edit.php')
          ->setEditThickbox(['width' => 700, 'height' => 500, 'title' => 'Edit Application']);
}

$Table->addSQL("SELECT * FROM application ORDER BY app_id");

$rows = DB::pass()->query($Table->sql_table)->toArray();

$res = [
    'rows' => $rows,
    'permissions' => [
        'hasEdit' => $has_edit,
        'hasCreate' => $has_create,
    ],
    'pagination' => [
        'total' => $Table->getTotalRecord(),
        'current' => get_current_page('page'),
        'pageSize' => $Table->getPageSize()
    ],
];

if (getValue('json')) {
    CommonService::resJson($res);
}

Vue::setData($res);
Vue::setTitle($page_title);
Vue::render('crm-general-application', 'admin');
?>
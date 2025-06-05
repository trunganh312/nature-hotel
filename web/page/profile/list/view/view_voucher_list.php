<style>
    td{
        text-align: center;
    }
</style>
<div class="col-md-12">
    <div class="infor-st-setting list_table">
        <div class="tabbable">
            <?=$Table->createTable()?>
            <?
            //Hiển thị data
            $data   =   $DB->query($Table->sql_table)->toArray();
            $stt    =   0;
            foreach ($data as $row) {
                $row['vou_quantity'] -= $row['vou_time_used'];
                $Table->setRowData($row);
                $stt++;
                ?>
                <?=$Table->createTR($stt, $row['vou_id']);?>
                <?=$Table->showFieldText('vou_code')?>
                <td>
                    <?php 
                    switch($row['vou_type']) {
                        case VOUCHER_TYPE_MONEY:
                            echo format_number($row['vou_value']) .'đ';
                        break;
                        case VOUCHER_TYPE_PERCENT:
                            echo $row['vou_value'] .'%';
                        break;
                    }
                    ?>
                </td>
                <?=$Table->showFieldNumber('vou_quantity')?>
                <?=$Table->showFieldDate('vou_time_expire')?>
                <?=$Table->showFieldDate('vou_time_create')?>
                <td>
                    <? if($row['vou_active'] && $row['vou_quantity'] > 0 && $row['vou_time_expire'] > CURRENT_TIME): ?>
                        <span class="badge bg-olive">Active</span>
                    <? else: ?>
                        <span class="badge bg-danger">Inactive</span>
                    <? endif; ?>
                </td>
                <?=$Table->closeTR($row['vou_id']);?>
                <?
            }
            ?>
            <?=$Table->closeTable();?>
        </div>
    </div>
</div>
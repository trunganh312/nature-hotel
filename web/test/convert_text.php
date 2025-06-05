<?
$type   =   isset($_POST['type']) ? (int)$_POST['type'] : 1;
$text   =   isset($_POST['text']) ? $_POST['text'] : '';

$text_convert   =   '';
if ($text != '') {
    if ($type == 1) {
        $text_convert   =   ucwords($text);
    } else if ($type == 2) {
        $text_convert   =   strtolower($text);
    } else if ($type == 3) {
        $text_convert   =   strtoupper($text);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Convert text</title>
    <style>
        input, select {
            padding: 3px 5px;
            height: 28px;
            border: 1px solid #cdcdcd;
        }
        input {
            width: 100%;
        }
        td {
            padding: 10px 5px;
        }
        tr td:first-child {
            width: 100px;
            text-align: right;
        }
        a, button {
            padding: 8px 20px;
            background-color: #f5f5f5;
            border: 1px solid #cdcdcd;
        }
        a {
            margin: 1px 0 0 10px;
            cursor: pointer;
        }
        #text_result {
            background-color: #f5f5f5;
        }
    </style>
    <script>
        function copy_to_clipboard() {
            /* Get the text field */
            var copyText = document.getElementById("text_result");
            
            /* Select the text field */
            copyText.select();
            
            /* Copy the text inside the text field */
            document.execCommand("copy");
            
            /* Show result */
            document.getElementById('btn_copy').innerHTML = 'Copied';
        }
    </script>
</head>
<body style="padding: 10px 30px;">
    <form action="" method="POST">
        <table style="width: 100%;">
            <tr>
                <td>Kiểu convert:</td>
                <td>
                    <select id="type" name="type">
                        <option value="1" <?=($type == 1 ? 'selected' : '')?>>Chữ thường &#8594; hoa đầu</option>
                        <option value="2" <?=($type == 2 ? 'selected' : '')?>>Chữ hoa &#8594; thường</option>
                        <option value="3" <?=($type == 3 ? 'selected' : '')?>>Sang tất cả chữ hoa</option>
                    </select>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>Nội dung:</td>
                <td>
                    <input type="text" id="text" name="text" value="<?=$text?>" />
                </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Convert</button></td>
                <td></td>
            </tr>
            <?
            if ($text_convert != '') {
                ?>
                <tr>
                    <td>Kết quả:</td>
                    <td>
                        <input type="text" id="text_result" readonly value="<?=$text_convert?>" />
                    </td>
                    <td style="width: 100px;">
                        <a onclick="copy_to_clipboard()" id="btn_copy">Copy</a>
                    </td>
                </tr>
                <?
            }
            ?>
        </table>
    </form>
</body>
</html>

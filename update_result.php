<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id = intval($_POST['match_id']);
    $result = $_POST['result'];

    $query = "UPDATE `matches` SET `result` = '$result' WHERE `id` = $match_id";
    if (mysqli_query($db, $query)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "更新失败: " . mysqli_error($db);
    }
}
?>

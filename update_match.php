<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单提交的数据
    $match_id = $_POST['match_id'];
    $class_a_name = $_POST['class_a'];
    $class_b_name = $_POST['class_b'];
    $start_time = $_POST['start_time'];
    $result = $_POST['result'];

    // 查询 class_a 的 id
    $query_a = "SELECT id FROM `classes` WHERE `class_name` = '$class_a_name' LIMIT 1";
    $result_a = mysqli_query($db, $query_a);
    $class_a_id = mysqli_fetch_assoc($result_a)['id'];

    // 查询 class_b 的 id
    $query_b = "SELECT id FROM `classes` WHERE `class_name` = '$class_b_name' LIMIT 1";
    $result_b = mysqli_query($db, $query_b);
    $class_b_id = mysqli_fetch_assoc($result_b)['id'];

    // 更新比赛信息（去掉了 field 字段）
    $query = "UPDATE `matches` SET 
                `class_a` = '$class_a_id', 
                `class_b` = '$class_b_id', 
                `start_time` = '$start_time', 
                `result` = '$result' 
              WHERE `id` = $match_id";

    if (mysqli_query($db, $query)) {
        echo "比赛更新成功！";
        // 跳转回比赛管理页面
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // 如果没有来源页面（可能是直接访问的页面），返回默认页面
            header('Location: admin.php');
            exit();
        }
    } else {
        echo "更新失败: " . mysqli_error($db);
    }
}
?>

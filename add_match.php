<?php
include 'db.php';

// 获取表单提交的数据
$class_a_name = $_POST['class_a'];
$class_b_name = $_POST['class_b'];
$start_time = $_POST['start_time'];
$field = $_POST['field'];

// 检查班级 A 是否存在
$query_a = "SELECT id FROM `classes` WHERE `class_name` = '$class_a_name'";
$result_a = mysqli_query($db, $query_a);
$row_a = mysqli_fetch_assoc($result_a);

// 检查班级 B 是否存在
$query_b = "SELECT id FROM `classes` WHERE `class_name` = '$class_b_name'";
$result_b = mysqli_query($db, $query_b);
$row_b = mysqli_fetch_assoc($result_b);

// 如果班级 A 或 B 不存在，终止执行并返回错误消息
if (!$row_a) {
    die('班级 A 不存在');
}

if (!$row_b) {
    die('班级 B 不存在');
}

// 获取班级 A 和 B 的 ID
$class_a_id = $row_a['id'];
$class_b_id = $row_b['id'];

// 插入比赛数据
$query_insert = "INSERT INTO `matches` (`class_a`, `class_b`, `start_time`, `field`, `result`) 
                 VALUES ('$class_a_id', '$class_b_id', '$start_time', '$field', NULL)";

if (mysqli_query($db, $query_insert)) {
    echo "比赛已成功添加！";
} else {
    echo "添加比赛时发生错误：" . mysqli_error($db);
}
?>

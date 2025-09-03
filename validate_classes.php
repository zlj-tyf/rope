<?php
include 'db.php';

$class_a = $_POST['class_a'];
$class_b = $_POST['class_b'];

// 检查班级是否存在
$query_a = "SELECT COUNT(*) AS count FROM `classes` WHERE `class_name` = '$class_a'";
$query_b = "SELECT COUNT(*) AS count FROM `classes` WHERE `class_name` = '$class_b'";

$result_a = mysqli_query($db, $query_a);
$result_b = mysqli_query($db, $query_b);

$row_a = mysqli_fetch_assoc($result_a);
$row_b = mysqli_fetch_assoc($result_b);

// 返回检查结果
echo json_encode([
    'class_a_exists' => $row_a['count'] > 0,
    'class_b_exists' => $row_b['count'] > 0
]);
?>

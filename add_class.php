<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = $_POST['class_name'];

    // 添加班级
    $query = "INSERT INTO `classes` (`class_name`) VALUES ('$class_name')";
    if (mysqli_query($db, $query)) {
        echo "班级添加成功";
    } else {
        echo "错误: " . mysqli_error($db);
    }
}
?>

<?php
$host = 'localhost';  // 数据库主机
$user = 'root';       // 数据库用户名
$pass = '123456';           // 数据库密码
$db_name = 'rope'; // 数据库名称

// 创建数据库连接
$db = mysqli_connect($host, $user, $pass, $db_name);

// 检查连接
if (!$db) {
    die("连接失败: " . mysqli_connect_error());
}
?>

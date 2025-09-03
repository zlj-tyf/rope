<?php
include 'db.php';

// 获取传递的JSON数据
$data = json_decode(file_get_contents('php://input'), true);

// 获取数据
$match_id = $data['match_id'];
$student_id = $data['student_id'];
$checked_in = $data['checked_in'];

// 检查数据库中是否已有该记录
$query = "SELECT * FROM `attendance` WHERE `match_id` = $match_id AND `student_id` = $student_id";
$result = mysqli_query($db, $query);
$attendance = mysqli_fetch_assoc($result);

if ($attendance) {
    // 如果存在，更新记录
    $query_update = "UPDATE `attendance` SET `checked_in` = $checked_in WHERE `match_id` = $match_id AND `student_id` = $student_id";
    $success = mysqli_query($db, $query_update);
    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Attendance updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
} else {
    // 如果不存在，插入新记录
    $query_insert = "INSERT INTO `attendance` (`match_id`, `student_id`, `checked_in`) VALUES ($match_id, $student_id, $checked_in)";
    $success = mysqli_query($db, $query_insert);
    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Attendance inserted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
    }
}
?>

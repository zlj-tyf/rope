<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$match_id = intval($data['match_id']);
$student_id = intval($data['student_id']);
$checked_in = intval($data['checked_in']); // 0 或 1

// 查询是否已有记录
$query = "SELECT * FROM `attendance` WHERE match_id = $match_id AND student_id = $student_id";
$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) > 0) {
    // 更新签到状态
    $update = "UPDATE `attendance` SET `checked_in` = $checked_in WHERE match_id = $match_id AND student_id = $student_id";
    mysqli_query($db, $update);
} else {
    // 插入新记录
    $insert = "INSERT INTO `attendance` (match_id, student_id, checked_in) VALUES ($match_id, $student_id, $checked_in)";
    mysqli_query($db, $insert);
}

echo json_encode(["success" => true, "checked_in" => $checked_in]);

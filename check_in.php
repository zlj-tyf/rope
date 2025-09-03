<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $match_id = $_POST['match_id'];

    // 检查学生是否已经签到
    $query = "SELECT * FROM `attendance` WHERE `match_id` = $match_id AND `student_id` = $student_id";
    $result = mysqli_query($db, $query);

    if (mysqli_num_rows($result) > 0) {
        // 更新签到状态
        $query_update = "UPDATE `attendance` SET `checked_in` = NOT `checked_in` WHERE `match_id` = $match_id AND `student_id` = $student_id";
        mysqli_query($db, $query_update);
    } else {
        // 如果没有记录，插入新的签到记录
        $query_insert = "INSERT INTO `attendance` (`match_id`, `student_id`, `checked_in`) VALUES ($match_id, $student_id, 1)";
        mysqli_query($db, $query_insert);
    }

    // 返回签到状态（0 或 1）
    $status_query = "SELECT `checked_in` FROM `attendance` WHERE `match_id` = $match_id AND `student_id` = $student_id";
    $status_result = mysqli_query($db, $status_query);
    $status = mysqli_fetch_assoc($status_result);
    echo $status['checked_in'];
}
?>

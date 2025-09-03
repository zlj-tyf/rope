<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $class_name = $_POST['class_name'];

    // 更新班级名称
    $query = "UPDATE `classes` SET `class_name` = '$class_name' WHERE `id` = $class_id";
    if (mysqli_query($db, $query)) {
        echo "班级名称更新成功<br>";
    } else {
        echo "更新失败: " . mysqli_error($db);
    }

    // 更新学生名单
    $students_names = explode("\n", $_POST['students']);
    
    // 清空原有学生记录
    $query = "DELETE FROM `students` WHERE `class_id` = $class_id";
    if (!mysqli_query($db, $query)) {
        echo "删除学生失败: " . mysqli_error($db);
    }

    // 插入新学生记录
    foreach ($students_names as $student_name) {
        $student_name = trim($student_name);  // 去除可能的前后空格
        if (!empty($student_name)) {
            $query = "INSERT INTO `students` (`class_id`, `name`) VALUES ('$class_id', '$student_name')";
            if (!mysqli_query($db, $query)) {
                echo "插入学生失败: " . mysqli_error($db);
            }
        }
    }

    // 返回到前一个页面
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // 如果没有来源页面（可能是直接访问的页面），可以返回默认页面
        header('Location: class_manage.php');
        exit();
    }
}
?>

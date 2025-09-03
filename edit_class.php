<?php
include 'db.php';

// 获取班级ID
$class_id = $_GET['id'];

// 获取班级信息
$query = "SELECT * FROM `classes` WHERE `id` = $class_id";
$class_result = mysqli_query($db, $query);
$class = mysqli_fetch_assoc($class_result);

// 获取班级中的学生
$query_students = "SELECT * FROM `students` WHERE `class_id` = $class_id";
$students_result = mysqli_query($db, $query_students);

// 获取所有学生的名字
$students_names = [];
while ($student = mysqli_fetch_assoc($students_result)) {
    $students_names[] = $student['name'];
}

// 将学生姓名以换行符连接起来，用于显示在多行文本框中
$students_text = implode("\n", $students_names);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>编辑班级</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FAF6EF; /* 使用 Portland Stone 背景色 */
            color: #10263B; /* 使用 Nottingham Blue 字体颜色 */
            margin: 0;
            padding: 0;
        }

        h2, h3 {
            color: #10263B; /* 使用 Nottingham Blue */
        }

        h2 {
            background-color: #33AFCD; /* 使用 80% Malaysia Sky Blue 作为标题背景 */
            padding: 10px;
            margin: 0;
        }

        form {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
        }

        label {
            font-size: 16px;
            color: #10263B;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #707D89; /* 使用 60% Nottingham Blue 边框色 */
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            background-color: #009BC1; /* 使用 Malaysia Sky Blue */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #33AFCD; /* 使用 80% Malaysia Sky Blue 作为按钮悬停色 */
        }

        textarea {
            resize: vertical;
            font-family: 'Courier New', Courier, monospace;
        }

        .student-list {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 10px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .student-list ul {
            list-style-type: none;
            padding: 0;
        }

        .student-list li {
            padding: 8px;
            background-color: #F3F4F5; /* 使用 5% Nottingham Blue 作为学生列表项背景 */
            margin-bottom: 5px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>编辑班级: <?= $class['class_name'] ?></h2>

    <form action="update_class.php" method="POST">
        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">

        <label for="class_name">班级名称:</label>
        <input type="text" name="class_name" value="<?= $class['class_name'] ?>" required>

        <h3>班级学生（姓名按行分开）</h3>
        <textarea name="students" rows="10" required><?= $students_text ?></textarea>

        <button type="submit">更新班级</button>
    </form>


    <div class="student-list">
        <h3>班级学生列表</h3>
        <ul>
            <?php foreach ($students_names as $student_name): ?>
                <li><?= $student_name ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- 回首页按钮 -->
<a href="index.php" class="back-to-home">
    回首页
</a>
<footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:999;">
    For tech support: Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
    &nbsp;|&nbsp; 
</footer>
<style>
    /* 悬浮按钮的样式 */
    .back-to-home {       
        z-index: 999;
        position: fixed;
        bottom: 20px;  /* 距离页面底部 20px */
        right: 20px;   /* 距离页面右边 20px */
        padding: 10px 20px;
        background-color: #10263B;  /* Nottingham Blue */
        color: white;
        font-size: 1.5em;
        border-radius: 50px;  /* 圆角 */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);  /* 阴影效果 */
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .back-to-home:hover {
        background-color: #33AFCD;  /* 80% Malaysia Sky Blue */
        transform: scale(1.1);  /* 放大效果 */
    }

    .back-to-home:active {
        background-color: #405162;  /* 80% Nottingham Blue */
        transform: scale(1);  /* 确保按下时没有放大效果 */
    }
</style>
</body>
</html>

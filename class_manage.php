<?php
include 'db.php';

// 获取所有班级
$query = "SELECT * FROM `classes`";
$result_classes = mysqli_query($db, $query);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>班级管理</title>
    <style>
        /* 主要字体 */
        body {
            font-family: Arial, sans-serif;
            background-color: #FAF6EF; /* Portland Stone */
            color: #10263B; /* Nottingham Blue */
            margin: 0;
            padding: 0;
        }

        h2, h3 {
            text-align: center;
            color: #10263B; /* Nottingham Blue */
        }

        h2 {
            margin-top: 30px;
        }

        /* 表单样式 */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        label {
            font-size: 16px;
            color: #405162; /* 80% Nottingham Blue */
            margin-bottom: 10px;
        }

        input[type="text"] {
            padding: 8px;
            margin-bottom: 10px;
            width: 200px;
            border: 1px solid #405162; /* 80% Nottingham Blue */
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #009BC1; /* Malaysia Sky Blue */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #33AFCD; /* 80% Malaysia Sky Blue */
        }

        /* 表格样式 */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #CFD4D8; /* 20% Nottingham Blue */
        }

        th {
            background-color: #10263B; /* Nottingham Blue */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #F3F4F5; /* 5% Nottingham Blue */
        }

        tr:hover {
            background-color: #F2FAFC; /* 5% Malaysia Sky Blue */
        }

        a {
            color: #66C3DA; /* 60% Malaysia Sky Blue */
            text-decoration: none;
        }

        a:hover {
            color: #33AFCD; /* 80% Malaysia Sky Blue */
        }
    </style>
</head>
<body>
    <h2>班级管理</h2>
    <form action="add_class.php" method="POST">
        <label for="class_name">班级名称:</label>
        <input type="text" name="class_name" required>
        <button type="submit">添加班级</button>
    </form>

    <h3>班级列表</h3>
    <table>
        <tr>
            <th>班级名称</th>
            <th>操作</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result_classes)): ?>
            <tr>
                <td><?= $row['class_name'] ?></td>
                <td><a href="edit_class.php?id=<?= $row['id'] ?>">编辑</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <!-- 回首页按钮 -->
<a href="index.php" class="back-to-home">
    回首页
</a>

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
</style><footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:99;">
    For tech support: Contact Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
    &nbsp;|&nbsp; 
</footer>
</body>
</html>

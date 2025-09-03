<?php
include 'db.php';

// 获取所有班级
$query_classes = "SELECT * FROM `classes`";
$result_classes = mysqli_query($db, $query_classes);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>管理员页面</title>
    <style>
        /* 全局样式 */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FAF6EF; /* Portland Stone */
            color: #10263B; /* Nottingham Blue */
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        h2, h3 {
            color: #10263B;
            font-size: 1.8rem;
            margin-bottom: 15px;
            margin-left: 20px;
        }

        /* 管理比赛 */
        .form-container {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            width: 90%;
            margin: 20px auto;
            box-sizing: border-box;
            border: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .form-container label {
            font-size: 1rem;
            font-weight: 600;
            color: #405162; /* 80% Nottingham Blue */
            margin-right: 10px;
        }

        .form-container input,
        .form-container select {
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #405162;
            border-radius: 6px;
        }

        .form-container text-input{
            width: 20px;
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #405162;
            border-radius: 6px;
        }

        .form-container button {
            background-color: #10263B; /* Nottingham Blue */
            color: white;
            padding: 10px 20px;
            font-size: 1rem;
            /* font-color:white; */
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 15px;
        }

        .form-container button:hover {
            background-color: #405162; /* 80% Nottingham Blue */
        }

        /* 比赛列表样式 */
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px 15px;
            text-align: center;
            font-size: 1rem;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #10263B; /* Nottingham Blue */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #9FA8B1; /* 40% Nottingham Blue */
        }

        tr:hover {
            background-color: #CFD4D8; /* 20% Nottingham Blue */
        }

        td input, td select {
            /* width: 150px; */
            padding: 10px;
            margin: 0;
            font-size: 1rem;
            border: 1px solid #405162;
            border-radius: 6px;
            width: auto;
            margin: 0;
        }

        td button {
            background-color: #405162; /* 80% Nottingham Blue */
            color:white;
            border-radius: 6px;
            padding: 8px 14px;
        }

        td button:hover {
            background-color: #10263B; /* Nottingham Blue */
        }

        /* 高亮未结束的比赛 */
        .highlight {
            background-color: #33AFCD; /* 80% Nottingham Blue */
            color: white;
        }

    </style>
</head>
<body>
    <h2>管理比赛</h2>

    <div class="form-container">
        <form action="add_match.php" method="POST" onsubmit="return validateClasses(event)">
            <label for="class_a">班级 A:</label>
            <input class="text-input" type="text" id="class_a" name="class_a" placeholder="请输入班级 A 名称" required>

            <label for="class_b">班级 B:</label>
            <input type="text" id="class_b" name="class_b" placeholder="请输入班级 B 名称" required>

            <label for="start_time">开始时间:</label>
            <input type="datetime-local" name="start_time" required>

            <label for="field">场地:</label>
            <select name="field">
                <option value="A">场地 A</option>
                <option value="B">场地 B</option>
            </select>

            <label hidden for="result">结果:</label>
            <input hidden type="radio" name="result" value="未结束" checked> 
            <input hidden type="radio" name="result" value="A胜利">
            <input hidden type="radio" name="result" value="B胜利">

            <button type="submit">添加比赛</button>
        </form>
    </div>

    <h3>比赛列表(高亮为待开始比赛)</h3>
    <table>
        <tr>
            <th>班级 A</th>
            <th>班级 B</th>
            <th>开始时间</th>
            <th>场地</th>
            <th>结果</th>
            <th>操作</th>
        </tr>
        
        <?php 
        // 获取所有比赛数据并显示
        $query_matches = "SELECT * FROM `matches`";
        $result_matches = mysqli_query($db, $query_matches);

        while ($match = mysqli_fetch_assoc($result_matches)): 
            // 获取班级 A 和 B 名称
            $class_a_query = "SELECT class_name FROM `classes` WHERE `id` = {$match['class_a']}";
            $class_a_result = mysqli_query($db, $class_a_query);
            $class_a = mysqli_fetch_assoc($class_a_result);

            $class_b_query = "SELECT class_name FROM `classes` WHERE `id` = {$match['class_b']}";
            $class_b_result = mysqli_query($db, $class_b_query);
            $class_b = mysqli_fetch_assoc($class_b_result);
        ?>
        
        <form action="update_match.php" method="POST">
            <tr class="<?= $match['result'] == '未结束'||date('Y-m-d H:i:s')<$match['start_time'] ? 'highlight' : '' ?>">
                <td>
                    <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                    <input type="text" name="class_a" value="<?= $class_a['class_name'] ?>" required>
                </td>
                <td>
                    <input type="text" name="class_b" value="<?= $class_b['class_name'] ?>" required>
                </td>
                <td>
                    <input type="datetime-local" name="start_time" value="<?= $match['start_time'] ?>" required>
                </td>
                <td>
                    <select name="field">
                        <option value="A" <?= $match['field'] == 'A' ? 'selected' : '' ?>>场地 A</option>
                        <option value="B" <?= $match['field'] == 'B' ? 'selected' : '' ?>>场地 B</option>
                    </select>
                </td>
                <td>
                    <input type="radio" name="result" value="未结束" <?= $match['result'] == '未结束' ? 'checked' : '' ?>> 未结束
                    <input type="radio" name="result" value="A胜利" <?= $match['result'] == 'A胜利' ? 'checked' : '' ?>> A胜利
                    <input type="radio" name="result" value="B胜利" <?= $match['result'] == 'B胜利' ? 'checked' : '' ?>> B胜利
                </td>
                <td>
                    <button type="submit">更新</button>
                </td>
            </tr>
        </form>

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
</style>  <footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:999;">
    For tech support: Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
    &nbsp;|&nbsp; 
</footer>
</body>
</html>

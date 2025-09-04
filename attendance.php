<?php
include 'db.php';

// 获取所有比赛列表
$query = "SELECT m.id, c1.class_name AS class_a, c2.class_name AS class_b
          FROM `matches` m
          JOIN `classes` c1 ON m.class_a = c1.id
          JOIN `classes` c2 ON m.class_b = c2.id";
$result_matches = mysqli_query($db, $query);

// 获取所有学生信息
$query_students = "SELECT * FROM `students`";
$result_students = mysqli_query($db, $query_students);

// 如果传递了 match_id，查询该比赛的学生
$students_a = [];
$students_b = [];
$match = null;
if (isset($_GET['match_id'])) {
    $match_id = $_GET['match_id'];

    // 获取比赛详细信息
    $query = "SELECT m.*, c1.class_name AS class_a_name, c2.class_name AS class_b_name 
              FROM `matches` m
              JOIN `classes` c1 ON m.class_a = c1.id
              JOIN `classes` c2 ON m.class_b = c2.id
              WHERE m.id = $match_id";
    $match_result = mysqli_query($db, $query);
    $match = mysqli_fetch_assoc($match_result);

    // 获取两个班级的学生
    $query_a = "SELECT * FROM `students` WHERE `class_id` = {$match['class_a']}";
    $result_a = mysqli_query($db, $query_a);

    $query_b = "SELECT * FROM `students` WHERE `class_id` = {$match['class_b']}";
    $result_b = mysqli_query($db, $query_b);

    // 获取比赛的签到记录
    $query_attendance = "SELECT * FROM `attendance` WHERE `match_id` = $match_id";
    $result_attendance = mysqli_query($db, $query_attendance);
    $attendance_data = [];
    while ($attendance = mysqli_fetch_assoc($result_attendance)) {
$attendance_data[$attendance['student_id']] = intval($attendance['checked_in']);
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>检录页面</title>
    <style>
        /* 基本样式 */
        body {
            font-family: Arial, sans-serif;
            background-color: #FAF6EF; /* Portland Stone */
            color: #10263B; /* Nottingham Blue */
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        h2, h3 {
            text-align: center;
            color: #10263B; /* Nottingham Blue */
        }

        form {
            text-align: center;
            margin: 20px 0;
        }

        label, select {
            font-size: 1.2em;
            padding: 10px;
            margin: 10px;
        }

        select {
            border: 2px solid #405162; /* 80% Nottingham Blue */
            background-color: #F2FAFC; /* 5% Malaysia Sky Blue */
            border-radius: 5px;
            font-size: 1.2em;
        }

        option {
            padding: 10px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #CFD4D8; /* 20% Nottingham Blue */
        }

        th {
            background-color: #10263B; /* Nottingham Blue */
            color: white;
        }

        .class-column {
            background-color: #405162; /* 80% Nottingham Blue */
            color: white;
        }

        .student-row {
            cursor: pointer;
        }

        .status {
            font-weight: bold;
            text-align: center;
        }

        .checked-in {
            color: #33AFCD; /* 80% Malaysia Sky Blue */
        }

        .not-checked-in {
            color: red; 
        }

        .student-row:hover {
            background-color: #F0F4F8;
        }
    </style>
</head>
<body>
    <h2>请选择比赛进行检录</h2>
    <form action="" method="GET">
        <label for="match_id">比赛：</label>
        <select name="match_id" id="match_id" onchange="this.form.submit()">
            <option value="">选择比赛</option>
            <?php while ($match_row = mysqli_fetch_assoc($result_matches)): ?>
                <option value="<?= $match_row['id'] ?>" <?= isset($match_id) && $match_id == $match_row['id'] ? 'selected' : '' ?>>
                    <?= $match_row['class_a'] ?> VS <?= $match_row['class_b'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if (isset($match_id) && $match): ?>
        <h3>检录：<?= $match['class_a_name'] ?> VS <?= $match['class_b_name'] ?></h3>

        <table>
            <tr>
                <th class="class-column"><?= $match['class_a_name'] ?> 学生</th>
                <th class="class-column"><?= $match['class_b_name'] ?> 学生</th>
            </tr>
            <tr>
                <td>
                    <table>
                        <?php while ($student = mysqli_fetch_assoc($result_a)): ?>
                            <tr class="student-row" data-id="<?= $student['id'] ?>">
                                <td><?= $student['name'] ?></td>
                                <td class="status" data-status="<?= isset($attendance_data[$student['id']]) ? $attendance_data[$student['id']] : 0 ?>">
                                    <span class="<?= isset($attendance_data[$student['id']]) && $attendance_data[$student['id']] ? 'checked-in' : 'not-checked-in' ?>">
                                        <?= isset($attendance_data[$student['id']]) && $attendance_data[$student['id']] ? '已签到' : '未签到' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </td>
                <td>
                    <table>
                        <?php while ($student = mysqli_fetch_assoc($result_b)): ?>
                            <tr class="student-row" data-id="<?= $student['id'] ?>">
                                <td><?= $student['name'] ?></td>
                                <td class="status" data-status="<?= isset($attendance_data[$student['id']]) ? $attendance_data[$student['id']] : 0 ?>">
                                    <span class="<?= isset($attendance_data[$student['id']]) && $attendance_data[$student['id']] ? 'checked-in' : 'not-checked-in' ?>">
                                        <?= isset($attendance_data[$student['id']]) && $attendance_data[$student['id']] ? '已签到' : '未签到' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <script>
        document.querySelectorAll('.student-row').forEach(function(row) {
            row.addEventListener('click', function() {
                const studentId = row.getAttribute('data-id');
                const statusCell = row.querySelector('.status');
                const currentStatus = statusCell.getAttribute('data-status');
                const newStatus = currentStatus === '1' ? '0' : '1';

                // 更新显示状态
                statusCell.setAttribute('data-status', newStatus);
                const statusText = statusCell.querySelector('span');
                if (newStatus === '1') {
                    statusText.textContent = '已签到';
                    statusText.classList.remove('not-checked-in');
                    statusText.classList.add('checked-in');
                } else {
                    statusText.textContent = '未签到';
                    statusText.classList.remove('checked-in');
                    statusText.classList.add('not-checked-in');
                }

                // 发送签到状态更新到服务器
                fetch('update_attendance.php', {
                    method: 'POST',
                    body: JSON.stringify({ match_id: <?= $match_id ?>, student_id: studentId, checked_in: newStatus }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).catch(error => console.log(error));
            });
        });
    </script>

    <a href="index.php" class="back-to-home">回首页</a>

    <style>
        .back-to-home {
            z-index: 9999;
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #10263B;
            color: white;
            font-size: 1.5em;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .back-to-home:hover {
            background-color: #33AFCD;
            transform: scale(1.1);
        }
        .back-to-home:active {
            background-color: #405162;
            transform: scale(1);
        }
    </style>

    <footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:999;">
        For tech support: Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
    </footer>
</body>
</html>

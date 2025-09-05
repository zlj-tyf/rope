<?php
include 'db.php';

// 获取所有比赛（按时间排序）
$matches_result = mysqli_query($db, "SELECT m.*, c1.class_name AS class_a_name, c2.class_name AS class_b_name
                                     FROM `matches` m
                                     JOIN `classes` c1 ON m.class_a = c1.id
                                     JOIN `classes` c2 ON m.class_b = c2.id
                                     ORDER BY m.start_time ASC");
$matches = [];
while ($row = mysqli_fetch_assoc($matches_result)) {
    $matches[] = $row;
}

// 当前比赛 ID
$current_match_id = $_GET['match_id'] ?? ($matches[0]['id'] ?? null);

$current_match_index = 0;
$current_match = null;
foreach ($matches as $i => $m) {
    if ($m['id'] == $current_match_id) {
        $current_match_index = $i;
        $current_match = $m;
        break;
    }
}

// 找上一场/下一场
$prev_match = $current_match_index > 0 ? $matches[$current_match_index - 1] : null;
$next_match = $current_match_index < count($matches) - 1 ? $matches[$current_match_index + 1] : null;

// 准备学生数据和签到信息
$attendance_data = [];
if ($current_match) {
    $query_a = "SELECT * FROM `students` WHERE `class_id` = {$current_match['class_a']}";
    $result_a = mysqli_query($db, $query_a);

    $query_b = "SELECT * FROM `students` WHERE `class_id` = {$current_match['class_b']}";
    $result_b = mysqli_query($db, $query_b);

    $query_attendance = "SELECT * FROM `attendance` WHERE `match_id` = {$current_match['id']}";
    $result_attendance = mysqli_query($db, $query_attendance);
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
        body { font-family: Arial, sans-serif; background:#FAF6EF; color:#10263B; margin:0; padding:0;font-size:2em; }
        h2,h3 { text-align:center; color:#10263B; }
        form { text-align:center; margin:20px 0; }
        select { font-size:1em; padding:10px; margin:10px; border:2px solid #405162; background:#F2FAFC; border-radius:5px; }
        table { width:80%; margin:20px auto; border-collapse:collapse; }
        th,td { padding:10px; border:1px solid #CFD4D8; }
        th { background:#10263B; color:white; }
        .class-column { background:#405162; color:white; }
        .student-row { cursor:pointer; }
        .status { font-weight:bold; text-align:center; }
        .checked-in { color:#33AFCD; }
        .not-checked-in { color:red; }
        .student-row:hover { background:#F0F4F8; }
        .nav-button { background:#707D89; color:white; font-size:20px; padding:10px 20px; margin:20px; border:none; border-radius:8px; cursor:pointer; }
        .back-to-home { position:fixed; bottom:20px; right:20px; padding:10px 20px; background:#10263B; color:white; font-size:1em; border-radius:50px; text-decoration:none;z-index:9999; }
        .back-to-home:hover { background:#33AFCD; transform:scale(1.1); }
    </style>
</head>
<body>
    <h2>请选择比赛进行检录</h2>
    <form action="" method="GET">
        <label for="match_id">比赛：</label>
        <select name="match_id" id="match_id" onchange="this.form.submit()">
            <?php foreach ($matches as $m): ?>
                <option value="<?= $m['id'] ?>" <?= $m['id']==$current_match_id?'selected':'' ?>>
                    <?= $m['class_a_name'] ?> VS <?= $m['class_b_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($current_match): ?>
        <h3>检录：<?= $current_match['class_a_name'] ?> VS <?= $current_match['class_b_name'] ?></h3>
        <table>
            <tr>
                <th class="class-column"><?= $current_match['class_a_name'] ?> 学生</th>
                <th class="class-column"><?= $current_match['class_b_name'] ?> 学生</th>
            </tr>
            <tr>
                <td>
                    <table>
                        <?php while ($student = mysqli_fetch_assoc($result_a)): ?>
                            <tr class="student-row" data-id="<?= $student['id'] ?>">
                                <td><?= $student['name'] ?></td>
                                <td class="status" data-status="<?= $attendance_data[$student['id']] ?? 0 ?>">
                                    <span class="<?= ($attendance_data[$student['id']] ?? 0) ? 'checked-in' : 'not-checked-in' ?>">
                                        <?= ($attendance_data[$student['id']] ?? 0) ? '已签到' : '未签到' ?>
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
                                <td class="status" data-status="<?= $attendance_data[$student['id']] ?? 0 ?>">
                                    <span class="<?= ($attendance_data[$student['id']] ?? 0) ? 'checked-in' : 'not-checked-in' ?>">
                                        <?= ($attendance_data[$student['id']] ?? 0) ? '已签到' : '未签到' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </td>
            </tr>
        </table>

        <div style="text-align:center;">
            <?php if ($prev_match): ?>
                <form method="GET" style="display:inline-block;">
                    <input type="hidden" name="match_id" value="<?= $prev_match['id'] ?>">
                    <button type="submit" class="nav-button">上一场比赛</button>
                </form>
            <?php endif; ?>
            <?php if ($next_match): ?>
                <form method="GET" style="display:inline-block;">
                    <input type="hidden" name="match_id" value="<?= $next_match['id'] ?>">
                    <button type="submit" class="nav-button">下一场比赛</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script>
        document.querySelectorAll('.student-row').forEach(function(row) {
            row.addEventListener('click', function() {
                const studentId = row.getAttribute('data-id');
                const statusCell = row.querySelector('.status');
                const currentStatus = statusCell.getAttribute('data-status');
                const newStatus = currentStatus === '1' ? '0' : '1';
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
                fetch('update_attendance.php', {
                    method: 'POST',
                    body: JSON.stringify({ match_id: <?= $current_match_id ?>, student_id: studentId, checked_in: newStatus }),
                    headers: { 'Content-Type': 'application/json' }
                }).catch(error => console.log(error));
            });
        });
    </script>

    <a href="index.html" class="back-to-home">回首页</a>

    <footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:999;">
        For tech support: Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;">scylz12@nottingham.edu.cn</a>)
    </footer>
</body>
</html>

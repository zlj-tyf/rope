<?php
include 'db.php';

// --- Display Section Logic ---
$query_matches = "SELECT * FROM `matches` ORDER BY `start_time` ASC";
$result_matches = mysqli_query($db, $query_matches);
$matches = [];
while ($row = mysqli_fetch_assoc($result_matches)) {
    $matches[] = $row;
}
function getClassName($class_id) {
    global $db;
    $query = "SELECT `class_name` FROM `classes` WHERE `id` = $class_id";
    $result = mysqli_query($db, $query);
    $class = mysqli_fetch_assoc($result);
    return $class['class_name'] ?? '未知班级';
}

// --- Admin Section Logic ---
$query_classes = "SELECT * FROM `classes`";
$result_classes = mysqli_query($db, $query_classes);

// --- Class Management Section Logic ---
$query = "SELECT * FROM `classes`";
$result_class_manage = mysqli_query($db, $query);

// --- Edit Class Logic ---
$edit_mode = false;
$edit_class_id = '';
$edit_class_name = '';
$edit_students_text = '';
if (isset($_GET['edit_class_id'])) {
    $edit_class_id = intval($_GET['edit_class_id']);
    $get_edit_class = mysqli_query($db, "SELECT * FROM `classes` WHERE id = $edit_class_id");
    if ($row = mysqli_fetch_assoc($get_edit_class)) {
        $edit_mode = true;
        $edit_class_name = $row['class_name'];
        $students_result = mysqli_query($db, "SELECT * FROM `students` WHERE `class_id` = $edit_class_id");
        $students_names = [];
        while ($student = mysqli_fetch_assoc($students_result)) {
            $students_names[] = $student['name'];
        }
        $edit_students_text = implode("\n", $students_names);
    }
}

// --- Edit Class Submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_class_submit'])) {
    $class_id = intval($_POST['edit_class_id']);
    $new_name = mysqli_real_escape_string($db, $_POST['edit_class_name']);
    $students_names = explode("\n", $_POST['edit_students']);
    mysqli_query($db, "UPDATE `classes` SET class_name='$new_name' WHERE id=$class_id");
    mysqli_query($db, "DELETE FROM `students` WHERE `class_id` = $class_id");
    foreach ($students_names as $student_name) {
        $student_name = trim($student_name);
        if (!empty($student_name)) {
            mysqli_query($db, "INSERT INTO `students` (`class_id`, `name`) VALUES ('$class_id', '$student_name')");
        }
    }
    header("Location: admin_geek.php");
    exit();
}

// --- Clear Match Result ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_match_result'])) {
    $match_id = intval($_POST['match_id']);
    mysqli_query($db, "UPDATE `matches` SET result=NULL WHERE id=$match_id");
    header("Location: admin_geek.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>拔河管理后台</title>
    <style>
        /* --- 页面统一圆角 --- */
button, input, select, textarea, .match, .result-buttons button {
    border-radius:6px; /* 统一圆角半径 */
}
/* --- 比赛结果按钮样式 --- */
.result-buttons {
    display: flex;
    width: 100%;
    border-radius: 6px;
    overflow: hidden;
}

/* 按钮统一样式 */
.result-buttons button {
    flex: 1;
    padding: 8px 0;
    margin: 2px;            /* 保持所有状态 margin 一致 */
    cursor: pointer;
    border: 1px solid #61dafb; /* 保持边框统一，不随状态变化 */
    border-radius: 6px;
    font-size: 0.95em;
    font-family: inherit;
    background: #181818;    /* 默认背景色 */
    color: #61dafb;          /* 默认文字色 */
    transition: background-color 0.2s, color 0.2s; /* 只动画背景色和文字色 */
}

/* 激活状态 */
.result-buttons button.active {
    background: #61dafb;
    color: #181818;
}

/* 清空按钮默认样式 */
.result-buttons button.clear-default {
    background: #61dafb;
    color: #181818;
}


/* 默认黑底蓝字 */
.result-buttons button.default { background:#181818;color:#61dafb; }
/* 默认清空按钮蓝底黑字 */
.result-buttons button.clear-default { background:#61dafb;color:#181818;border:1px solid #181818; }
/* 激活蓝底黑字 */
.result-buttons button.active { background:#61dafb;color:#181818;border:1px solid #61dafb; }

        /* --- 全局样式 --- */
        body {margin:0;background:#181818;color:#eee;font-family:'Fira Mono','Consolas','Menlo',monospace;height:90vh;overflow-x:hidden;}
        .big-title{width:100vw;text-align:center;font-size:3.3em;font-weight:bold;letter-spacing:0.1em;color:#61dafb;padding:34px 0 18px 0;background:linear-gradient(90deg,#181818 60%,#232323 100%);border-bottom:2px solid #222;box-shadow:0 0 24px #111 inset;font-family:inherit;}
        .container{display:flex;height:calc(90vh - 100px);max-width:100vw;overflow:visible;box-sizing:border-box;}
        .column{box-sizing:border-box;overflow-y:auto;overflow-x:hidden;padding:0;background:#191919;box-shadow:0 0 24px #111 inset;transition:background 0.2s;min-width:0;display:flex;flex-direction:column;height:100%;}
        .left{width:20vw;min-width:200px;background:linear-gradient(135deg,#20232a 60%,#282c34 100%);border-right:1px solid #222;}
        .middle{width:50vw;min-width:340px;background:linear-gradient(135deg,#181818 60%,#232323 100%);border-right:1px solid #222;}
        .right{width:30vw;min-width:240px;background:linear-gradient(135deg,#232323 60%,#282c34 100%);}
        .terminal-title{background:#232323;color:#61dafb;padding:0.75em 1em;font-size:1.1em;border-bottom:1px solid #222;letter-spacing:0.05em;font-weight:bold;font-family:inherit;}
        .terminal-content{padding:1em 1.5em;font-size:0.98em;font-family:inherit;}
        ::selection{background:#61dafb;color:#181818;}
        a{color:#61dafb;text-decoration:underline dashed;}
        a:hover{text-decoration:underline solid;background:#222;}
        .terminal-content.display-center{display:flex;flex-direction:column;align-items:center;}
        ::-webkit-scrollbar{width:8px;background:#222;}
        ::-webkit-scrollbar-thumb{background:#333;border-radius:4px;}
        table{width:100%;margin:18px 0;border-collapse:collapse;background:#181818;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.09);overflow:hidden;color:#eee;font-family:inherit;}
        th,td{padding:10px;text-align:center;border:1px solid #333;font-size:0.97em;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        th{background-color:#232323;color:#61dafb;}
        tr:nth-child(even){background-color:#21252b;}
        tr:hover{background-color:#333;}
        label{font-size:1em;color:#61dafb;margin-bottom:6px;margin-right:8px;font-family:inherit;}
        input[type="text"],input[type="datetime-local"],select{padding:8px;margin-bottom:8px;border:1px solid #61dafb;border-radius:4px;font-size:0.97em;background:#232323;color:#eee;font-family:inherit;box-sizing:border-box;}
        .right input[type="text"]#edit_class_name,.right input[type="text"][name="class_name"]{width:60%;min-width:80px;}
        button{background-color:#61dafb;color:#181818;padding:7px 18px;border:none;border-radius:4px;cursor:pointer;font-size:0.98em;margin:0 4px;font-family:inherit;}
        /* button:hover{background-color:#232323;color:#61dafb;border:1px solid #61dafb;} */
        .match{margin:10px 0;padding:10px 12px;border:1px solid #333;border-radius:5px;font-size:1em;background:#21252b;transition:background 0.2s;font-family:inherit;width:80%;min-width:160px;text-align:center;}
        .match.highlight{background-color:#181f25;border:1.5px solid #61dafb;}
        .match-header{font-weight:bold;font-size:1.07em;margin-bottom:5px;color:#61dafb;font-family:inherit;letter-spacing:0.04em;}
        .status{font-weight:bold;margin-top:4px;font-family:inherit;}
        .status.pending{color:orange;}
        .status.in-progress{color:#66ff66;}
        .status.completed{color:#ff6666;}
        .clear-btn{background:#444;color:#fff;border:1px solid #61dafb;border-radius:3px;font-size:0.9em;padding:4px 8px;margin-left:8px;cursor:pointer;transition:background 0.2s;}
        .clear-btn:hover{background:#e74c3c;color:#fff;border-color:#e74c3c;}
        .back-to-home{z-index:999;position:fixed;bottom:20px;right:20px;padding:10px 20px;background-color:#232323;color:#61dafb;font-size:1.25em;border-radius:40px;box-shadow:0 4px 8px rgba(0,0,0,0.22);text-decoration:none;display:inline-block;text-align:center;border:1.5px solid #61dafb;transition:background-color 0.3s, transform 0.3s;font-family:inherit;}
        .back-to-home:hover{background-color:#61dafb;color:#232323;transform:scale(1.08);}
        .back-to-home:active{background-color:#405162;color:#fff;transform:scale(1);}
        footer{position:fixed;left:0;bottom:0;width:100vw;background:#232323;border-top:1px solid #222;box-shadow:0 -2px 8px rgba(52,152,219,0.08);padding:14px 0 10px 0;color:#eee;font-size:1.13em;text-align:center;letter-spacing:0.03em;font-family:inherit;}
        footer a{color:#61dafb;text-decoration:underline dashed;}
        footer a:hover{color:#fff;text-decoration:underline solid;background:#333;}
        @media (max-width:1100px){.container{flex-direction:column;height:auto;}.column{width:100vw !important;min-width:unset;margin-bottom:16px;}}

        /* --- Admin 中间列输入框调窄 --- */
        .middle input[type="text"]{
            width:100%;
            max-width:10vw;
        }

        
    </style>
</head>
<body>
<div class="big-title">拔河管理后台</div>
<div class="container">
    <!-- Display Section -->
    <div class="column left">
        <div class="terminal-title">📺 Display</div>
        <div class="terminal-content display-center">
            <?php foreach ($matches as $match): ?>
                <div class="match<?php echo ($match['result'] === '未结束') ? ' highlight' : ''; ?>">
                    <div class="match-header">
                        <span><?php echo getClassName($match['class_a']); ?></span>
                        <span style="color:#eee;">vs</span>
                        <span><?php echo getClassName($match['class_b']); ?></span>
                    </div>
                    <div style="font-size:0.96em;">时间: <?php echo $match['start_time']; ?></div>
                    <div class="status <?php
    if (empty($match['result']) || $match['result'] === '未结束') echo 'pending';
    else echo 'completed';
?>">
<?php
    if (empty($match['result']) || $match['result'] === '未结束') {
        echo '未结束';
    } else {
        // 根据 result 显示获胜班级名称
        if ($match['result'] === 'A胜利') echo getClassName($match['class_a'])." 胜利";
        elseif ($match['result'] === 'B胜利') echo getClassName($match['class_b'])." 胜利";
        else echo $match['result'];
    }
?>
</div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Admin Section -->
    <div class="column middle">
        <div class="terminal-title">🛠️ Admin</div>
        <div class="terminal-content">
            <form action="add_match.php" method="POST" style="margin-bottom: 1em;">
                <label for="class_a">班级 A:</label>
                <input type="text" id="class_a" name="class_a" required>
                <label for="class_b">班级 B:</label>
                <input type="text" id="class_b" name="class_b" required>
                <label for="start_time">开始时间:</label>
                <input type="datetime-local" name="start_time" required>
                <button type="submit">添加比赛</button>
            </form>

            <table>
                <tr>
                    <th>班级 A</th>
                    <th>班级 B</th>
                    <th>开始时间</th>
                    <th>结果</th>
                    <th>操作</th>
                </tr>
                <?php foreach ($matches as $match): ?>
                <tr>
                    <form action="update_match.php" method="POST">
                        <td>
                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                            <input type="text" name="class_a" value="<?= getClassName($match['class_a']) ?>" required>
                        </td>
                        <td><input type="text" name="class_b" value="<?= getClassName($match['class_b']) ?>" required></td>
                        <td><input type="datetime-local" name="start_time" value="<?= $match['start_time'] ?>" required></td>
                        <td>
                            <div class="result-buttons">
                                <?php
                                    $classAName = getClassName($match['class_a']);
                                    $classBName = getClassName($match['class_b']);
                                    $result = $match['result'] ?? '';

                                    $btnAClass = $btnBClass = $btnClearClass = '';
                                    if (empty($result)) {
                                        $btnAClass = 'default';
                                        $btnBClass = 'default';
                                        $btnClearClass = 'clear-default';
                                    } else {
                                        $btnAClass = ($result === 'A胜利') ? 'active' : 'default';
                                        $btnBClass = ($result === 'B胜利') ? 'active' : 'default';
                                        $btnClearClass = (empty($result)) ? 'active' : 'default';
                                    }
                                ?>
                                <button type="submit" name="result" value="A胜利" class="<?= $btnAClass ?>"><?= htmlspecialchars($classAName) ?></button>
                                <button type="submit" name="result" value="B胜利" class="<?= $btnBClass ?>"><?= htmlspecialchars($classBName) ?></button>
                                <button type="submit" name="result" value="" class="<?= $btnClearClass ?>">清空</button>
                            </div>
                        </td>
                        <td>
                            <button type="submit"">更新</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- Class Management Section -->
    <div class="column right">
        <div class="terminal-title">🏷️ Class Management</div>
        <div class="terminal-content">
            <?php if ($edit_mode): ?>
                <h3 style="text-align:center;color:#61dafb;padding-top:12px;">编辑班级</h3>
                <form method="POST" style="margin-bottom: 1em;">
                    <input type="hidden" name="edit_class_id" value="<?= $edit_class_id ?>">
                    <label for="edit_class_name">班级名称:</label>
                    <input type="text" id="edit_class_name" name="edit_class_name" value="<?= htmlspecialchars($edit_class_name) ?>" required>
                    <label for="edit_students">班级学生（姓名按行分开）:</label>
                    <textarea name="edit_students" rows="8" style="width:100%;background:#232323;color:#eee;border:1px solid #61dafb;font-family:'Fira Mono',monospace;resize:vertical;" required><?= htmlspecialchars($edit_students_text) ?></textarea>
                    <button type="submit" name="edit_class_submit">保存</button>
                    <a href="admin_geek.php" style="margin-left:20px;color:#eee;">取消</a>
                </form>
            <?php else: ?>
                <h3 style="text-align:center;color:#61dafb;padding-top:12px;">添加班级</h3>
                <form action="add_class.php" method="POST">
                    <label for="class_name">班级名称:</label>
                    <input type="text" name="class_name" required>
                    <button type="submit">添加班级</button>
                </form>
            <?php endif; ?>
            <h3 style="text-align:center;color:#61dafb;padding-top:12px;">班级列表</h3>
            <table>
                <tr><th>班级名称</th><th>操作</th></tr>
                <?php
                $result_class_manage = mysqli_query($db, $query);
                while ($row = mysqli_fetch_assoc($result_class_manage)): ?>
                    <tr>
                        <td><?= $row['class_name'] ?></td>
                        <td><a href="admin_geek.php?edit_class_id=<?= $row['id'] ?>">编辑</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
<a href="index.html" class="back-to-home">回首页</a>
<footer>
    For tech support: Contact Lijie ZHOU (20809020
    <a href="mailto:scylz12@nottingham.edu.cn">scylz12@nottingham.edu.cn</a>)
</footer>
</body>
</html>

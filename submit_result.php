<?php
include 'db.php';

// 获取所有比赛
$matches_result = mysqli_query($db, "SELECT * FROM `matches` ORDER BY `start_time` ASC");
$matches = [];
while($row = mysqli_fetch_assoc($matches_result)) {
    $matches[] = $row;
}

// 获取班级名
function getClassName($class_id) {
    global $db;
    $res = mysqli_query($db, "SELECT `class_name` FROM `classes` WHERE `id` = $class_id");
    $row = mysqli_fetch_assoc($res);
    return $row['class_name'] ?? '未知班级';
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['winner'])) {
    $match_id = intval($_POST['match_id']);
    $winner = $_POST['winner']; // A 或 B

    // 更新比赛结果
    $match = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `matches` WHERE id=$match_id"));
    if ($winner === 'A') {
        $result = 'A胜利';
    } else {
        $result = 'B胜利';
    }
    mysqli_query($db, "UPDATE `matches` SET `result`='$result' WHERE `id`=$match_id");

    // 找下一场比赛
    $next_index = 0;
    foreach ($matches as $i => $m) {
        if ($m['id'] == $match_id) {
            $next_index = $i + 1;
            break;
        }
    }
    if (isset($matches[$next_index])) {
        header("Location: submit_result.php?match_id=".$matches[$next_index]['id']);
        exit;
    } else {
        header("Location: submit_result.php?message=finished");
        exit;
    }
}

// 当前比赛
$current_match_id = $_GET['match_id'] ?? $matches[0]['id'];
$current_match_index = 0;
$current_match = null;
foreach ($matches as $i => $m) {
    if ($m['id'] == $current_match_id) {
        $current_match_index = $i;
        $current_match = $m;
        break;
    }
}

// 找上一场比赛
$prev_match = $current_match_index > 0 ? $matches[$current_match_index - 1] : null;

// 获取当前比赛的胜利状态
$winner_a = $current_match['result'] === 'A胜利';
$winner_b = $current_match['result'] === 'B胜利';

// 生成按钮文本
$text_a = ($winner_a ? '🎉 ' : '') . getClassName($current_match['class_a']) . '<br/> 获胜';
$text_b = ($winner_b ? '🎉 ' : '') . getClassName($current_match['class_b']) . '<br/> 获胜';
?>

<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>比赛结果提交</title>
<style>
body {
    font-size: 35px; /* 原来50px，缩小约30% */
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 40px;
    background: #FAF6EF;
}

select {
    font-size: 35px; /* 缩小 */
    padding: 10px;
    margin-bottom: 30px;
}

button {
    font-size: 48px; /* 缩小，保持一致 */
    padding: 20px 40px;
    margin: 20px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: transform 0.2s;
    width: 400px; /* 所有按钮宽度固定，确保一致 */
    display: inline-block;
}

button:hover {
    transform: scale(1.05);
}

.button-a { background-color: #33AFCD; color: white; }
.button-b { background-color: #10263B; color: white; }
.button-prev { background-color: #707D89; color: white; font-size: 36px;}

.message { font-size: 35px; color: #E74C3C; margin-top: 30px; }
</style>

</head>
<body>

<h1>比赛结果提交</h1>

<?php if(isset($_GET['message']) && $_GET['message'] === 'finished'): ?>
    <div class="message">所有比赛已完成！</div>
<?php else: ?>
<form method="POST" id="resultForm">
<div>
    <label for="match_id">选择比赛：</label>
    <select name="match_id" id="matchSelect" onchange="location.href='submit_result.php?match_id='+this.value;">
        <?php foreach($matches as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $m['id']==$current_match['id']?'selected':'' ?>>
                <?= date('Y-m-d H:i', strtotime($m['start_time'])) ?>：
                <?= getClassName($m['class_a']) ?> VS <?= getClassName($m['class_b']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div>
    <button type="submit" name="winner" value="A" class="button-a"><?= $text_a ?></button>
    <button type="submit" name="winner" value="B" class="button-b"><?= $text_b ?></button>
</div>

</form>

<?php if ($prev_match): ?>
    <form method="GET" style="margin-top:40px;">
        <input type="hidden" name="match_id" value="<?= $prev_match['id'] ?>">
        <button type="submit" class="button-prev">返回上一场比赛</button>
    </form>
<?php endif; ?>

<?php endif; ?>
<a href="index.php" class="back-to-home">
    回首页
</a>

<style>
    /* 悬浮按钮的样式 */
    .back-to-home {
        z-index: 999;
        font-size:24px;
        position: fixed;
        bottom: 20px;  /* 距离页面底部 20px */
        right: 20px;   /* 距离页面右边 20px */
        padding: 10px 20px;
        background-color: #10263B;  /* Nottingham Blue */
        color: white;
        /* font-size: 1.5em; */
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
</style><footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:99;font-size:24px;">
    For tech support: Contact Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
    &nbsp;|&nbsp; 
</footer>
</body>
</html>

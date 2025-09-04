<?php
include 'db.php';

// 获取所有比赛
$query = "SELECT * FROM `matches` ORDER BY `start_time` ASC";
$result = mysqli_query($db, $query);

$matches = [];
while ($row = mysqli_fetch_assoc($result)) {
    $matches[] = $row;
}

// 获取班级名的辅助函数
function getClassName($class_id) {
    global $db;
    $query = "SELECT `class_name` FROM `classes` WHERE `id` = $class_id";
    $result = mysqli_query($db, $query);
    $class = mysqli_fetch_assoc($result);
    return $class['class_name'] ?? '未知班级';
}
function getWinnerColor($class_id) {
    if ($class_id >= 1 && $class_id <= 10) {           // 商学院
        return '#9be3a4'; // Malaysia Sky Blue
    } elseif ($class_id >= 11 && $class_id <= 23) {   // 人文
        return '#ea5632'; // Nottingham Blue
    } elseif ($class_id >= 24 && $class_id <= 40) {   // 理工
        return '#e9d26a'; // 40% Nottingham Blue
    } else {                                           // 测试用户
        return 'blue'; // 红色
    }
}
// 先确定每场比赛的状态
$prev_result = true;
$match_statuses = [];
foreach ($matches as $index => $match) {
    $status = 'pending';
    if (!empty($match['result'])) {
        $status = 'completed';
        $prev_result = true;
    } elseif ($prev_result && empty($match['result'])) {
        $status = 'in-progress';
        $prev_result = false;
    } else {
        $prev_result = false;
    }
    $match_statuses[$index] = $status;
}

// 找出下一场需要检录的比赛（高亮下方的一场）
$next_match = null;
foreach ($match_statuses as $i => $status) {
    if ($status === 'in-progress' && isset($matches[$i+1])) {
        $next_match = $matches[$i+1];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>比赛大屏</title>
<style>
:root {
    --winner-color: #33AFCD;
    --gray-pending: #E0E0E0;
    --blue-inprogress: #99D7E6;
    --completed-color: #D0D0D0; 
    --team-bg: #9FA8B1;
    --team-text: #10263B;
    --portland-stone: #FAF6EF;
    --result-red: #E74C3C;
}

body {
    font-family: Arial, sans-serif;
    background-color: var(--portland-stone);
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 95vh;
}

.header {
    width: 100%;
    text-align: center;
    padding: 16px 0;
    background-color: var(--portland-stone);
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-title {
    font-size: 75px;
    font-weight: bold;
    margin-bottom: 8px;
    color: var(--team-text);
}

.next-match-box {
    background-color: #10263B;
    color: white;
    padding: 12px;
    border-radius: 12px;
    font-size: 90px;
    font-weight: bold;
}

.match-list {
    width: 100%;
    max-width: 800px;
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
}

.match {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 8px 0;
    padding: 12px;
    border-radius: 10px;
    background-color: var(--team-bg);
    transition: background-color 0.3s;
}

.match.pending { background-color: var(--gray-pending); }
.match.highlight { background-color: var(--blue-inprogress); }
.match.completed { background-color: var(--completed-color); }

.team {
    flex: 1;
    text-align: center;
    padding: 12px;
    border-radius: 20px;
    font-weight: bold;
    color: var(--team-text);
    margin: 0 15px;
    background-color: var(--team-bg);
}

.team.winner {
    background-color: var(--winner-color);
    color: white;
}

.time {
    flex: 0 0 120px;
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    color: var(--team-text);
}

.status {
    font-size: 14px;
    margin-top: 6px;
    text-align: center;
}

.status.completed { color: var(--result-red); }

.back-to-home {
    z-index: 999;
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 10px 20px;
    background-color: #10263B;
    color: white;
    font-size: 1.2em;
    border-radius: 50px;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.back-to-home:hover { background-color: var(--winner-color); transform: scale(1.1);}
.back-to-home:active { background-color: #405162; transform: scale(1);}
</style>
</head>
<body>

<div class="header">
    <div class="page-title">比赛大屏</div>
    <?php if ($next_match): ?>
    <div class="next-match-box">
        <?= getClassName($next_match['class_a']) ?> 和 <?= getClassName($next_match['class_b']) ?> 请前往检录台
    </div>
    <?php endif; ?>
</div>

<div class="match-list">
<?php 
foreach ($matches as $i => $match):
    $status = $match_statuses[$i];

    $class_a_name = getClassName($match['class_a']);
    $class_b_name = getClassName($match['class_b']);

    $winner_a = ($status === 'completed' && strpos($match['result'], 'A胜利') !== false);
    $winner_b = ($status === 'completed' && strpos($match['result'], 'B胜利') !== false);
    // echo $winner_a.' | '.$winner_b;
    // 胜利班级名字替换
    if ($winner_a) $class_a_name = ' 🎉 '.getClassName($match['class_a']);
    if ($winner_b) $class_b_name = ' 🎉 '.getClassName($match['class_b']);
?>
<div class="match <?= $status==='in-progress'?'highlight':($status==='pending'?'pending':'completed'); ?>">
    <div class="team" <?= $winner_a ? 'style="background-color:'.getWinnerColor($match['class_a']).';color:white;"' : '' ?>>
    <?= $class_a_name ?>
</div>


    <div class="time">
        <?= date('H:i', strtotime($match['start_time'])) ?> <!-- 预计时间 -->
        <div class="status <?= $status==='completed'?'completed':'' ?>">
<?php
if ($status === 'completed') {
    if ($winner_a) {
        echo getClassName($match['class_a']) . '胜利';
    } elseif ($winner_b) {
        echo getClassName($match['class_b']) . '胜利';
    } else {
        echo '';
    }
} elseif ($status === 'in-progress') {
    echo '进行中';
} else {
    echo '未开始';
}
?>

        </div>
    </div>
<div class="team" <?= $winner_b ? 'style="background-color:'.getWinnerColor($match['class_b']).';color:white;"' : '' ?>>
    <?= $class_b_name ?>
</div></div>
<?php endforeach; ?>
</div>

<a href="index.php" class="back-to-home">回首页</a>
<footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:99;">
    For tech support: Contact Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
</footer>

<script>
window.onload = function(){
    const currentMatch = document.querySelector('.highlight');
    if(currentMatch){
        currentMatch.scrollIntoView({behavior:'smooth', block:'center'});
    }
};
</script>

</body>
</html>

<?php
include 'db.php';

// 获取当前时间
$current_time = date('Y-m-d H:i:s');

// 获取所有比赛信息，按开始时间排序
$query = "SELECT * FROM `matches` ORDER BY `start_time` ASC";
$result = mysqli_query($db, $query);

// 获取所有比赛
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
    return $class['class_name'] ?? '未知班级'; // 如果没有找到班级名，则返回“未知班级”
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>比赛大屏</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* 使用诺丁汉官方颜色 */
        :root {
            --nottingham-blue: #10263B;
            --80-nottingham-blue: #405162;
            --60-nottingham-blue: #707D89;
            --40-nottingham-blue: #9FA8B1;
            --20-nottingham-blue: #CFD4D8;
            --5-nottingham-blue: #F3F4F5;
            --portland-stone: #FAF6EF;
            --40-portland-stone: #FDFBF9;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--portland-stone);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .matches-container {
            width: 90%;
            max-width: 1200px;
            background-color: var(--white);
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            height: 90vh;
        }

        .matches-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--nottingham-blue);
            text-align: center;
            margin-bottom: 20px;
        }

        .match {
            background-color: var(--5-nottingham-blue);
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .match.highlight {
            background-color:  #99D7E6;
            /* color: white; */
        }

        .match p {
            margin: 6px 0;
        }

        .match .status {
            font-weight: bold;
        }

        /* 标红显示获胜者 */
        .match .win {
            color: red;
        }

        .status.pending {
            color: var(--60-nottingham-blue);
        }

        .status.in-progress {
            color: var(--nottingham-blue);
        }

        .status.completed {
            color: red;
        }

        .match strong {
            color: var(--nottingham-blue);
        }

        .match:hover {
            background-color: var(--40-nottingham-blue);
            cursor: pointer;
        }

        /* 样式紧凑 */
        .match p {
            font-size: 14px;
        }

        /* 控制自动刷新时间 */
        @media screen and (max-width: 600px) {
            .matches-container {
                padding: 10px;
                width: 95%;
            }
            .match p {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- 比赛列表 -->
    <div class="matches-container">
        <div class="matches-title">比赛列表</div>
        <?php foreach ($matches as $match): 
            $status = 'pending'; // 默认状态为待开始
            // 如果比赛的开始时间已过，判断其状态
            if ($current_time >= $match['start_time']) {
                if ($match['result']) {
                    $status = 'completed'; // 已结束
                } else {
                    $status = 'in-progress'; // 正在进行中
                }
            }
            
            // 获取班级名称
            $class_a_name = getClassName($match['class_a']);
            $class_b_name = getClassName($match['class_b']);
            
            // 处理比赛结果，替换A和B为班级名
            $result = $match['result'];
            if ($result) {
                $result = str_replace('A', $class_a_name, $result);
                $result = str_replace('B', $class_b_name, $result);
            }
        ?>
            <div class="match <?php if ($status == 'in-progress') echo 'highlight'; ?>" id="match-<?= $match['id'] ?>">
                <p><strong><?= ($status == 'completed' && strpos($result, 'A胜利') !== false) ? "<span class='win'>$class_a_name</span>" : $class_a_name ?> VS <?= ($status == 'completed' && strpos($result, 'B胜利') !== false) ? "<span class='win'>$class_b_name</span>" : $class_b_name ?></strong></p>
                <p>时间：<?= $match['start_time'] ?></p>
                <p class="status <?= $status ?>">
                    <?php 
                    if ($status == 'completed') {
                        echo "结果：{$result}";
                    } else {
                        echo ucfirst($status);
                    }
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        window.onload = function() {
            // 自动滚动到当前进行中的比赛
            const currentMatch = document.querySelector('.highlight');
            if (currentMatch) {
                currentMatch.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        };
    </script><footer style="position:fixed; left:0; bottom:0; width:100%; background:#fff; border-top:1px solid #e0e6ed; box-shadow:0 -2px 8px rgba(52,152,219,0.08); padding:12px 0; color:#666; font-size:1em; text-align:center; z-index:999;">
    For tech support: Lijie ZHOU (20809020 <a href="mailto:scylz12@nottingham.edu.cn" style="color:#2980b9;text-decoration:none;">scylz12@nottingham.edu.cn</a>)
    &nbsp;|&nbsp; 
</footer>
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
</style>
</body>
</html>

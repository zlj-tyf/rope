<?php
// admin_geek.php

// Left column: Display page (import display.php)
ob_start();
include('display.php');
$display_content = ob_get_clean();

// Middle column: Current admin.php (import admin.php, but exclude header/footer if present)
ob_start();
include('admin.php');
$admin_content = ob_get_clean();

// Right column: Class management page (assume class_management.php exists)
ob_start();
if (file_exists('class_management.php')) {
    include('class_management.php');
} else {
    $class_content = "<div style='padding:2em;text-align:center;color:#888;'>Class management page not found.</div>";
}
$class_content = ob_get_clean();

// Some geek-like styling (dark theme, monospace, grid system, terminal effect)
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Geek Admin Panel</title>
<style>
    body {
        margin: 0;
        background: #181818;
        color: #eee;
        font-family: 'Fira Mono', 'Consolas', 'Menlo', monospace;
        height: 100vh;
        overflow: hidden;
    }
    .container {
        display: flex;
        height: 100vh;
        width: 100vw;
    }
    .column {
        border-right: 1px solid #222;
        box-sizing: border-box;
        overflow-y: auto;
        padding: 0;
        background: #191919;
        box-shadow: 0 0 24px #111 inset;
        transition: background 0.2s;
    }
    .column:last-child {
        border-right: none;
    }
    .left {
        width: 30vw;
        min-width: 240px;
        background: linear-gradient(135deg, #20232a 60%, #282c34 100%);
    }
    .middle {
        width: 40vw;
        min-width: 320px;
        background: linear-gradient(135deg, #181818 60%, #232323 100%);
    }
    .right {
        width: 30vw;
        min-width: 240px;
        background: linear-gradient(135deg, #232323 60%, #282c34 100%);
    }
    .terminal-title {
        background: #232323;
        color: #61dafb;
        padding: 0.75em 1em;
        font-size: 1.1em;
        border-bottom: 1px solid #222;
        letter-spacing: 0.05em;
        font-weight: bold;
    }
    .terminal-content {
        padding: 1em 1.5em;
        font-size: 0.98em;
    }
    ::selection {
        background: #61dafb;
        color: #181818;
    }
    a {
        color: #61dafb;
        text-decoration: underline dashed;
    }
    a:hover {
        text-decoration: underline solid;
        background: #222;
    }
    /* Scrollbar styling */
    ::-webkit-scrollbar {
        width: 8px;
        background: #222;
    }
    ::-webkit-scrollbar-thumb {
        background: #333;
        border-radius: 4px;
    }
</style>
</head>
<body>
<div class="container">
    <div class="column left">
        <div class="terminal-title">📺 Display</div>
        <div class="terminal-content"><?php echo $display_content; ?></div>
    </div>
    <div class="column middle">
        <div class="terminal-title">🛠️ Admin</div>
        <div class="terminal-content"><?php echo $admin_content; ?></div>
    </div>
    <div class="column right">
        <div class="terminal-title">🏷️ Class Management</div>
        <div class="terminal-content"><?php echo isset($class_content) ? $class_content : ''; ?></div>
    </div>
</div>
</body>
</html>
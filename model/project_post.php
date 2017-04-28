<?php
    if (!isset($_POST['pid']) || !isset($_POST['owner']) || !isset($_POST['content'])
        || $_POST['pid'] == '' || $_POST['owner'] == '' || $_POST['content'] == '') {
        reply('Please input a post');
    }
    session_start();
    if (!isset($_SESSION['username'])) {
        reply('Please login');
    }
    $username = $_SESSION['username'];
    $pid = intval($_POST['pid']);
    $owner = intval($_POST['owner']);
    $content = $_POST['content'];
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

    //insert post into db, either comment or post
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    $q = null;
    if ($owner) {
        $q = "INSERT INTO ProjectUpdate(username, pid, comment, ctime) VALUES ("
            .":u, :p, :c, DATETIME());";
    } else {
        $q = "INSERT INTO Comment(username, pid, comment, ctime) VALUES ("
            .":u, :p, :c, DATETIME());";
    }
    $e = [':u' => $username, ':p' => $pid, ':c' => $content];
    $success = $db->runUpdate($q,$e);
    if ($success) {
        reply("Post successful");
    } else {
        reply("Something went wrong");
    }

    function reply($message) {
        echo $message;
        exit();
    }
?>
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
    $datetime = date('Y-m-d H:i:s');
    if ($owner) {
        $q = "INSERT INTO ProjectUpdate(username, pid, comment, ctime) VALUES ("
            .":u, :p, :c, :d);";
    } else {
        $q = "INSERT INTO Comment(username, pid, comment, ctime) VALUES ("
            .":u, :p, :c, :d);";
    }
    $e = [':u' => $username, ':p' => $pid, ':c' => $content, ':d' => $datetime];
    $success1 = $db->runUpdate($q,$e);

    //posting the update was a success, now save the media
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/media.php');
    //get uid of current post
    $e = [':u' => $username, ':p' => $pid, ':d' => $datetime];
    $results = $db->runSelect("SELECT uid FROM ProjectUpdate WHERE ctime=:d AND "
                             ."username=:u AND pid=:p;", $e);
    if (!$results) {
        reply('Could not upload files');
    }
    $pid = $results[0]['uid'];
    $success2 = uploadMedia($pid);

    //reply to client
    if ($success1 && $success2) {
        reply("Post successful");
    } else {
        reply("Something went wrong");
    }

    function reply($message) {
        echo $message;
        exit();
    }
?>
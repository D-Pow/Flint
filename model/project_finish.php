<?php
    if (!isset($_POST['pid']) || $_POST['pid'] == '') {
        reply('Something went wrong');
    }

    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }

    $pid = intval(htmlspecialchars($_POST['pid'], ENT_QUOTES, 'UTF-8'));

    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    $q = "UPDATE Project SET proj_completed=1, completion_time=DATETIME() WHERE pid=:p;";
    $success = $db->runUpdate($q, [':p' => $pid]);
    if ($success) {
        reply('success');
    } else {
        reply('Something went wrong');
    }

    /**
     * Reply to client
     */
    function reply($message) {
        echo $message;
        exit();
    }
?>
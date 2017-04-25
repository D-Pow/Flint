<?php
    if (!isset($_POST['pid']) || !isset($_POST['description'])) {
        reply('Please insert a description');
    }
    if (!isset($_POST['pname'])) {
        reply('Please insert a project title');
    }
    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }
    $username = $_SESSION['username'];
    $pid = intval($_POST['pid']);
    $pname = $_POST['pname'];
    $description = $_POST['description'];

    //update description and pname
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    $q = "UPDATE Project SET pname=:pname, description=:d WHERE pid=:p;";
    $e = [':pname' => $pname, ':p' => $pid, ':d' => $description];
    $success = $db->runUpdate($q,$e);
    if ($success) {
        reply("Changes successful");
    } else {
        reply("Something went wrong");
    }

    /**
     * Send reply to client
     */
    function reply($message) {
        echo $message;
        exit();
    }
?>
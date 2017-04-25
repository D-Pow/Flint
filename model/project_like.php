<?php
    if (!isset($_POST['pid'])) {
        reply('Error');
    }
    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }
    $username = $_SESSION['username'];
    $pid = intval($_POST['pid']);

    //insert like into database if not present
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
    $likes = Project::getLikes($pid);
    if (array_key_exists($username, $likes)) {
        //user already likes the project
        exit();
    }

    //insert new like into database
    $db = DB::getInstance();
    $q = "INSERT INTO Likes VALUES (:u, :p, DATETIME());";
    $e = [':p' => $pid, ':u' => $username];
    $success = $db->runUpdate($q, $e);
    if ($success) {
        reply("liked");
    }

    /**
     * Send reply to client
     */
    function reply($message) {
        echo $message;
        exit();
    }
?>
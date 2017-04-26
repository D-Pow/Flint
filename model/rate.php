<?php
    if (!isset($_POST['rating']) || $_POST['rating'] == "" || 
        !isset($_POST['pid']) || $_POST['pid'] == "") {
        reply("Please select a rating");
    }
    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }

    $username = $_SESSION['username'];
    $rating = intval($_POST['rating']);
    $pid = intval($_POST['pid']);

    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    //if rating already exists for this user, update the rating
    $results = $db->runSelect("SELECT username FROM Rating WHERE "
            ."pid=:p AND username=:u;", [':p' => $pid, ':u' => $username]);
    if ($results) {
        $q = "UPDATE Rating SET rating=:r,rtime=DATETIME() WHERE "
            ."username=:u AND pid=:p;";
    } else {
        $q = "INSERT INTO Rating VALUES (:u, :p, :r, DATETIME());";
    }
    $e = [':u' => $username, ':p' => $pid, ':r' => $rating];
    $success = $db->runUpdate($q, $e);
    if ($success) {
        reply("Thanks for rating!");
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
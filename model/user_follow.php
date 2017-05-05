<?php
    if (!isset($_POST['user']) || $_POST['user'] == '') {
        reply("Something went wrong");
    }

    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }

    $user = htmlspecialchars($_POST['user'], ENT_QUOTES, 'UTF-8');

    //Follow user
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    //check if user exists
    $exists = $db->runSelect("SELECT username FROM user WHERE username=:f;",
            [':f' => $user]);
    //if exists, insert into Follows
    if ($exists) {
        $q = "INSERT INTO Follows VALUES (:u, :f);";
        $e = [':u' => $_SESSION['username'], ':f' => $user];
        $success = $db->runUpdate($q, $e);
        if ($success) {
            reply('success');
        } else {
            reply("Something went wrong");
        }
    } else {
        reply("Something went wrong");
    }


    function reply($message) {
        echo $message;
        exit();
    }
?>
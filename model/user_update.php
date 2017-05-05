<?php
    if (!isset($_POST['email']) || !isset($_POST['addr']) || !isset($_POST['city']) || 
        !isset($_POST['state']) || !isset($_POST['interests'])) {
        reply('Please enter values');
    }
    $email = $_POST['email'];
    $addr = $_POST['addr'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $interests = $_POST['interests'];

    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $addr = htmlspecialchars($addr, ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');
    $state = htmlspecialchars($state, ENT_QUOTES, 'UTF-8');
    $interests = htmlspecialchars($interests, ENT_QUOTES, 'UTF-8');

    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }

    //update user fields
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    $q = "UPDATE User SET email=:e, str_addr=:s, ucity=:c, ustate=:s, interests=:i "
        ."WHERE username=:u;";
    $e = [
        ':e' => $email,
        ':s' => $addr,
        ':c' => $city,
        ':s' => $state,
        ':i' => $interests,
        ':u' => $_SESSION['username']
    ];
    $success = $db->runUpdate($q, $e);
    if ($success) {
        reply("Changes saved!");
    } else {
        reply("Something went wrong");
    }

    function reply($message) {
        echo $message;
        exit();
    }

?>
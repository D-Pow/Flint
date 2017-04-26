<?php
    if (!isset($_POST['title']) || !isset($_POST['description']) ||
        !isset($_POST['minfunds']) || !isset($_POST['maxfunds']) ||
        !isset($_POST['date'])) {
        reply('Please enter values in all fields');
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $minfunds = $_POST['minfunds'];
    $maxfunds = $_POST['maxfunds'];
    $date = $_POST['date'];

    if ($title == '' || $description == '' || $minfunds == ''
        || $maxfunds == '' || $date == '') {
        reply('Please enter values in all fields');
    }

    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }

    //create new project
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    $q = "INSERT INTO Project(username, pname, description, post_time, proj_completed, "
        ."minfunds, maxfunds, camp_end_time, camp_finished, camp_success) "
        ."VALUES (:user, :pname, :descr, DATETIME(), :compl, :minfunds, :maxfunds, "
        .":camp_end_time, :camp_fin, :camp_suc);";
    $e = [
        ':user' => $_SESSION['username'],
        ':pname' => $title,
        ':descr' => $description,
        ':compl' => 0,
        ':minfunds' => $minfunds,
        ':maxfunds' => $maxfunds,
        ':camp_end_time' => $date." 23:59:59",
        ':camp_fin' => 0,
        ':camp_suc' => 0
    ];
    $success = $db->runUpdate($q, $e);
    if ($success) {
        reply('Project posted!');
    } else {
        reply("Something went wrong");
    }

    function reply($message) {
        echo $message;
        exit();
    }

?>
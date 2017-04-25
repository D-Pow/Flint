<?php
    if (!isset($_POST['donation']) || !isset($_POST['pid']) ||
            $_POST['donation'] == '' ||
            $_POST['pid'] == '' || intval($_POST['pid']) == 0) {
        reply("Please enter a donation amount");
    }
    if (intval($_POST['donation']) == 0) {
        reply("Please enter a number greater than 0");
    }
    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login to donate");
    }
    $amount = intval($_POST['donation']);
    $username = $_SESSION['username'];
    $pid = intval($_POST['pid']);

    //insert donation into database or update
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    //if user already donated before, add to their donation amount
    $q = "SELECT username FROM Donation WHERE pid=:p AND username=:u;";
    $e = [':p' => $pid, ':u' => $username];
    $results = $db->runSelect($q, $e);
    $success = null;
    if ($results) {
        //add to their donation amount and update the pledge_time
        $update = "UPDATE Donation SET amount=amount+:a, pledge_time=DATETIME() "
                . "WHERE username=:u AND pid=:p;";
        $entries = [':a' => $amount, ':u' => $username, ':p' => $pid];
        $success = $db->runUpdate($update, $entries);
        
    } else {
        //if user hasn't donated before, insert new value
        $insert = "INSERT INTO Donation VALUES (:u, :p, :a, DATETIME(), 0, NULL);";
        $entries = [
            ':u' => $username,
            ':p' => $pid,
            ':a' => $amount
        ];
        $success = $db -> runUpdate($insert, $entries);
    }
    if ($success) {
        //get current funds
        require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/donation.php');
        $currentFunds = Donation::getTotalDonations($pid);
        reply($currentFunds);
    } else {
        reply("Something went wrong");
    }

    /**
     * Sends reply back to client
     */
    function reply($message) {
        echo $message;
        exit();
    }
?>
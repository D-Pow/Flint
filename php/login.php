<?php
    /**
     * Note: PDO prepared statements are relatively
     * safe from SQL injection, but we still need many
     * other safety checks on user input.
     */
    if (isset($_POST['createNew'])) {
        $createNewUser = intval($_POST['createNew']);
    } else {
        $createNewUser = 0;
    }
    if ((!isset($_POST['username'])) || (!isset($_POST['password']))  ) {
        reply("no values");
    }
    if ($createNewUser && ((!isset($_POST['name'])) || (!isset($_POST['ccn']))) ) {
        reply("no values");
    }
    $username = $_POST["username"];
    $password = $_POST["password"];
    if ($createNewUser == 1) {
        $name = $_POST["name"];
        $ccn = $_POST["ccn"];
    }
    if ($username == "" || $password == "") {
        reply("no values");
    }
    if ($createNewUser && ($ccn == "" || $name == "")) {
        reply("no values");
    }
    
    //include($_SERVER['DOCUMENT_ROOT'].'/Flint/php/db.php');
    include('db.php');
    $db = new DB();
    
    if ($createNewUser===1) {
        //create new user
        $query = "SELECT username FROM user;";
        $results = $db->runSelect($query, null);
        if ($results) {
            foreach ($results as $row) {
                if ($row['username'] == $username) {
                    reply("user exists");
                }
            }
        }
        //insert values into database
        $hashedPassword = hash("sha256", $password);
        $query = "INSERT INTO user (username, password, uname, ccn) VALUES (:user, :pass, :uname, :ccn);";
        $entries = array(
                ':user' => $username,
                ':pass' => $hashedPassword,
                ':uname'=> $name,
                ':ccn'  => intval($ccn)
            );
        $success = $db->runUpdate($query, $entries);
        if ($success) {
            acceptLogin($username);
            reply("accept login");
        } else {
            reply("create failed");
        }
    } else {
        //load user from database
        $query = "SELECT password FROM user WHERE username=:user;";
        $entries = array(
                ':user' => $username
            );
        $results = $db->runSelect($query, $entries);
        if ($results) {
            $hashedPassword = hash("sha256", $password);
            $row = $results[0];
            $correctPass = $row['password'];
            if (!($correctPass == $hashedPassword)) {
                //reject request
                reply("wrong password");
            } else {
                acceptLogin($username);
                reply("accept login");
            }
        } else {
            //no users with that username
            reply("no usernames");
        }
    }

    function reply($message) {
        echo $message;
        exit();
    }

    function acceptLogin($username) {
        //Set username in php session
        session_start();
        $_SESSION['username'] = $username;
    }

?>
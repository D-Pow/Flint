<?php
    /**
     * Self-contained login script
     */
    //verify that the input was appropriate and correct
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
    
    require($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    
    //begin login
    if ($createNewUser===1) {
        //create new user
        $query = "SELECT lower(username) AS username FROM user;";
        $results = $db->runSelect($query, null);
        if ($results) {
            foreach ($results as $row) {
                if ($row['username'] == strtolower($username)) {
                    reply("user exists");
                }
            }
        }
        //insert values into database
        $hashedPassword = hash("sha256", $password);
        $query = "INSERT INTO user (username, password, uname, ccn, last_login) "
                . "VALUES (:user, :pass, :uname, :ccn, :lt);";
        $entries = array(
                ':user' => $username,
                ':pass' => $hashedPassword,
                ':uname'=> $name,
                ':ccn'  => intval($ccn),
                ':lt'   => date('0-0-0 0:0:0')  //make last login = never
            );
        $success = $db->runUpdate($query, $entries);
        if ($success) {
            acceptLogin($username);
        } else {
            reply("create failed");
        }
    } else {
        //load user from database
        $query = "SELECT password FROM User WHERE lower(username)=:user;";
        $entries = array(
                ':user' => strtolower($username)
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
        //Set username in php session and update last login time.
        //Last login time is used to update what appears on the
        //person's home feed
        global $db;
        $result = $db->runSelect("SELECT last_login FROM User WHERE lower(username)=:u",
            array(":u" => strtolower($username)));
        $cTime = date("Y-m-d H:i:s");  //current time
        if ($result) {
            $lTime = $result[0]['last_login'];  //last time the user logged in
        } else {
            //error
            echo "error";
            exit();
        }

        //update new last login time
        $success = $db->runUpdate("UPDATE User SET last_login = :lt WHERE lower(username)=:u",
            array(':lt' => $cTime, ':u' => strtolower($username)));
        
        //get correct username capitalization
        $result = $db->runSelect("SELECT username FROM User WHERE lower(username)=:u;",
                [':u'=>strtolower($username)]);
        $username = $result[0]['username'];
        //Set username and last login time in session
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['last_login'] = $lTime;   //stored as string
        reply("accept login");
    }

?>
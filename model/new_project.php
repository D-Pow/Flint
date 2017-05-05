<?php
    if (!isset($_POST['title']) || !isset($_POST['description']) ||
        !isset($_POST['minfunds']) || !isset($_POST['maxfunds']) ||
        !isset($_POST['date'])) {
        reply('Please enter values in all fields');
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $minfunds = intval($_POST['minfunds']);
    $maxfunds = intval($_POST['maxfunds']);
    $date = $_POST['date'];
    $tags = $_POST['tags'];

    //sanitize input of XSS
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $date = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
    for ($i = 0; $i < count($tags); $i++) {
        //remove whitespace and sanitize
        $tags[$i] = trim(htmlspecialchars($tags[$i], ENT_QUOTES, 'UTF-8'));
    }

    if ($title == '' || $description == '' || $minfunds == ''
        || $maxfunds == '' || $date == '') {
        reply('Please enter values in all fields');
    }

    session_start();
    if (!isset($_SESSION['username'])) {
        reply("Please login");
    }

    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();

    //make sure the user doesn't already have a project titled that title
    $results = $db->runSelect("SELECT pname FROM Project WHERE username=:u;",
            [':u' => $_SESSION['username']]);
    foreach ($results as $row) {
        if ($title == $row['pname']) {
            reply("A project with that name already exists");
        }
    }

    //create new project
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
    $success1 = $db->runUpdate($q, $e);

    //attach tags to project and insert into database if necessary
    //get pid of new project
    $r = $db->runSelect("SELECT pid FROM Project WHERE username=:u AND pname=:p "
        ."AND camp_end_time=:t;", [
            ':u' => $_SESSION['username'],
            ':p' => $title,
            ':t' => $date." 23:59:59"
        ]);
    $pid = $r[0]['pid'];
    $success2 = insertAndConnectTags($db, $tags, $pid);
    if ($success1 && $success2) {
        reply('Project posted!');
    } else {
        reply("Something went wrong");
    }

    /**
     * Inserts tags that don't exists in the database into the database.
     * Connects a project with all the tags in the $tags array
     * $tags is the array of user-inputted tags
     */
    function insertAndConnectTags($db, $tags, $pid) {
        if (!$tags) {
            //don't insert blank tags
            return true;
        }
        $q = "SELECT name FROM Tags;";
        $results = $db->runSelect($q, null);
        $success = true;  //keep track of all queries to ensure success
        if ($results) {
            $allTags = [];
            //put all tags in an array
            foreach ($results as $row) {
                $allTags[$row['name']] = $row['name']; //makes faster lookup time
            }
            //insert tags in database and connect them to the project
            foreach ($tags as $tag) {  //for each tag in the user input
                //insert into database if tagname doesn't exist
                if (!array_key_exists($tag, $allTags)) {
                    //database automatically sets tid
                    $s = $db->runUpdate("INSERT INTO Tags(name) VALUES (:n);",
                            [':n' => $tag]);
                    $success = $success && $s;
                }
                //connect the project to the tag
                $r = $db->runSelect("SELECT tid FROM Tags WHERE name=:n;", [':n' => $tag]);
                if (!$r) {
                    $success = false;
                    break;
                }
                $tid = $r[0]['tid'];
                $s = $db->runUpdate("INSERT INTO Ptags VALUES (:p, :t);",
                        [':p' => $pid, ':t' => $tid]);
                $success = $success && $s;
            }
        } else {
            return false;
        }
        return $success;
    }

    function reply($message) {
        echo $message;
        exit();
    }

?>
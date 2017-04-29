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
    $tags = $_POST['tags'];

    //sanitize input
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $pname = htmlspecialchars($pname, ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    for ($i = 0; $i < count($tags); $i++) {
        $tags[$i] = htmlspecialchars($tags[$i], ENT_QUOTES, 'UTF-8');
    }

    //update description and pname
    require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/db.php');
    $db = DB::getInstance();
    $q = "UPDATE Project SET pname=:pname, description=:d WHERE pid=:p;";
    $e = [':pname' => $pname, ':p' => $pid, ':d' => $description];
    $success1 = $db->runUpdate($q,$e);

    //deal with updated tags
    $success2 = updateAndConnectTags($db, $tags, $pid);

    if ($success1 && $success2) {
        reply("Changes successful");
    } else {
        reply("Something went wrong");
    }

    /**
     * Inserts tags that don't exists in the database into the database.
     * Connects a project with all the tags in the $tags array
     * $tags is the array of user-inputted tags
     */
    function updateAndConnectTags($db, $tags, $pid) {
        //if no tags are present
        if (!$tags || $tags[0] == '') {
            $q = "DELETE FROM Ptags WHERE pid=:p;";
            $success = $db->runUpdate($q, [':p' => $pid]);
            return $success;
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
            //get all old tags from the database to see if user removed any tags
            //and remove those tags
            $results = $db->runSelect("SELECT tid, name FROM Tags JOIN Ptags USING(tid) "
                ."WHERE pid=:p;", [':p' => $pid]);
            $presentTags = [];
            if ($results) {
                foreach ($results as $row) {
                    $presentTags[] = $row['name'];
                }
            }
            $toRemove = array_diff($presentTags, $tags);
            $toAdd = array_diff($tags, $presentTags);
            foreach ($toRemove as $tag) {

                $remove = "DELETE FROM Ptags WHERE pid=:p AND tid IN ("
                         ."SELECT tid FROM Tags WHERE name=:n);";
                $e = [':p' => $pid, ':n' => $tag];
                $s = $db->runUpdate($remove, $e);
                $success = $success && $s;
            }
            //insert tags in database and connect them to the project
            foreach ($toAdd as $tag) {  //for each tag in the user input
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

    /**
     * Send reply to client
     */
    function reply($message) {
        echo $message;
        exit();
    }
?>
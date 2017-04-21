<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        ?>
        <script>window.location="/Flint/index.html"</script>
        <?php
    }
    $username = $_SESSION['username'];
    include('db.php');
    $db = new DB();

    getFeed($username, $db);

    function getFeed($username, $db) {
        //recent actions by those the user follows
        $comments = getFollowedComments($username, $db);
        $likes = getFollowedLikes($username, $db);
        $donations = getFollowedDonations($username, $db);
        //projects that the user likes
        $projects = getLikedProjects($username, $db);
    }

    /**
     * Get specific details about a project, given its pid.
     */
    function getProjectInfo($pid, $db) {
        $q = "SELECT pid, pname, username, proj_completed, camp_finished, "
                . "post_time, completion_time FROM Project WHERE pid=:p";
        $e = array(":p"=>$pid);
        $results = $db->runSelect($q, $e);
        $arrayOfProject = $db->resultsToArray($results);
        //Only one project will be returned, so get the only project from
        //the array returned by db->resultsToArray()
        $project = $arrayOfProject[0];
        return $project;
    }

    /**
     * Get a list of project details for all projects
     * liked by a given user.
     */
    function getLikedProjects($username, $db) {
        $q = "SELECT pid, pname, username, proj_completed, camp_finished, "
                . "post_time, completion_time FROM Project WHERE pid IN "
                . "(SELECT pid FROM Likes WHERE username=:u);";
        $entries = array(":u" => $username);
        $results = $db->runSelect($q, $entries);
        if ($results) {
            return $db->resultsToArray($results);
        } else {
            return null;
        }
    }

    /**
     * Get all comments from those the user follows.
     */
    function getFollowedComments($username, $db) {
        $q = "SELECT cid, username, pid, comment, ctime FROM Comment WHERE "
                . "username IN ("
                . "SELECT follows FROM Follows WHERE username=:u);";
        $entries = array(":u" => $username);
        $results = $db->runSelect($q, $entries);
        if ($results) {
            return $db->resultsToArray($results);
        } else {
            return null;
        }
    }

    function getFollowedLikes($username, $db) {
        $q = "SELECT cid, username, pid, comment, ctime FROM Comment WHERE "
                . "username IN ("
                . "SELECT follows FROM Follows WHERE username=:u);";
        $entries = array(":u" => $username);
        $results = $db->runSelect($q, $entries);
        if ($results) {
            return $db->resultsToArray($results);
        } else {
            return null;
        }
    }

    function getFollowedDonations($username, $db) {
        $q = "SELECT cid, username, pid, comment, ctime FROM Comment WHERE "
                . "username IN ("
                . "SELECT follows FROM Follows WHERE username=:u);";
        $entries = array(":u" => $username);
        $results = $db->runSelect($q, $entries);
        if ($results) {
            return $db->resultsToArray($results);
        } else {
            return null;
        }
    }
?>
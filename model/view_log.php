<?php
    class ViewLog {

        /**
         * Logs a search keyword a user submitted
         */
        public static function logSearch($username, $keyword) {
            $db = DB::getInstance();
            $q = "INSERT INTO Searches VALUES (:u, :k, DATETIME());";
            $success = $db->runUpdate($q, [':u' => $username, ':k' => $keyword]);
            return $success;
        }

        /**
         * Logs when a user clicks on a project
         */
        public static function logProjectView($username, $pid) {
            $db = DB::getInstance();
            $q = "INSERT INTO ProjectViews VALUES (:u, :p, DATETIME());";
            $success = $db->runUpdate($q, [':u' => $username, ':p' => $pid]);
            return $success;
        }

        /**
         * Logs when a user clicks on a tag
         */
        public static function logTagView($username, $tag_name) {
            $db = DB::getInstance();
            //get the tid
            $q = "SELECT tid FROM Tags WHERE name=:tn;";
            $results = $db->runSelect($q, [':tn' => $tag_name]);
            if ($results) {
                $row = $results[0];
                $tid = intval($row['tid']);
                $q = "INSERT INTO TagViews VALUES (:u, :t, DATETIME());";
                $success = $db->runUpdate($q, [':u' => $username, ':t' => $tid]);
                return $success;
            } else {
                return false;
            }            
        }

        /**
         * Gets all recent searches for a given user
         */
        public static function getRecentSearches($username) {
            $db = DB::getInstance();
            $q = "SELECT search, MAX(stime) FROM Searches WHERE username=:u "
                ."GROUP BY search ORDER BY stime DESC;";
            $results = $db->runSelect($q, [':u' => $username]);
            if ($results) {
                $searches = [];
                foreach ($results as $row) {
                    $searches[] = $row['search'];
                }
                return $searches;
            } else {
                return null;
            }
        }

        /**
         * Gets all recent project views for a given user
         */
        public static function getRecentProjectViews($username) {
            $db = DB::getInstance();
            $q = "SELECT pname, pid, MAX(vtime) FROM ProjectViews AS pv JOIN "
                ."Project USING(pid) WHERE pv.username=:u GROUP BY pid ORDER "
                ."BY vtime DESC;";
            $results = $db->runSelect($q, [':u' => $username]);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    //put in an array to preserve the order of most-recent view
                    $projects[] = ['pname' => $row['pname'], 'pid' => $row['pid']];
                }
                return $projects;
            } else {
                return null;
            }
        }

        /**
         * Gets all recent tag views for a given user
         */
        public static function getRecentTagViews($username) {
            $db = DB::getInstance();
            $q = "SELECT tid, name, MAX(vtime) FROM TagViews JOIN Tags USING(tid) "
                ."WHERE username=:u GROUP BY tid, name ORDER BY vtime DESC;";
            $results = $db->runSelect($q, [':u' => $username]);
            if ($results) {
                $tags = [];
                foreach ($results as $row) {
                    $tags[] = $row['name'];
                }
                return $tags;
            } else {
                return null;
            }
        }

    }
?>
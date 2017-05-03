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
            $q = "WITH t AS (SELECT * FROM Searches WHERE username=:u ORDER BY stime DESC) "
                ."SELECT DISTINCT(search) AS keyword FROM t;";
            $results = $db->runSelect($q, [':u' => $username]);
            if ($results) {
                $searches = [];
                foreach ($results as $row) {
                    $searches[] = $row['keyword'];
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
            $q = "WITH t AS (SELECT pv.username,pname,pid FROM ProjectViews AS pv JOIN "
                ."Project USING(pid) WHERE pv.username=:u ORDER BY vtime DESC) "
                ."SELECT DISTINCT(pname) AS pname,pid FROM t;";
            $results = $db->runSelect($q, [':u' => $username]);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $projects[$row['pname']] = $row['pid'];
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
            $q = "WITH t AS (SELECT tid, name FROM TagViews JOIN Tags USING(tid) "
                ."WHERE username=:u ORDER BY vtime DESC) SELECT DISTINCT(name) "
                ."AS tag_name FROM t;";
            $results = $db->runSelect($q, [':u' => $username]);
            if ($results) {
                $tags = [];
                foreach ($results as $row) {
                    $tags[] = $row['tag_name'];
                }
                return $tags;
            } else {
                return null;
            }
        }

    }
?>
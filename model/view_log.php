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
    }
?>
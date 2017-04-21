<?php
    /**
     * Simple class wrapper for easy use of database
     * and for formatting results of queries.
     */
    class DB {

        /**
         * Runs select query with optional entries.
         * Returns results or null if no results.
         */
        public function runSelect($query, $entries) {
            try {
                $db = new PDO("sqlite:../flint.db");
                $statement = $db->prepare($query);
                if ($entries) {
                    $statement->execute($entries);
                } else {
                    $statement->execute();
                }
                $results = $statement->fetchAll();
                if ($results) {
                    return $results;
                } else {
                    return null;
                }
            } finally {
                unset($db);
            }
        }

        /**
         * Runs an update query, such as UPDATE or INSERT
         * Returns true or false for success.
         */
        public function runUpdate($query, $entries) {
            try {
                $db = new PDO("sqlite:../flint.db");
                $statement = $db->prepare($query);
                if ($entries) {
                    $success = $statement->execute($entries);
                } else {
                    $success = $statement->execute();
                }
                return $success;
            } finally {
                unset($db);
            }
        }

        /**
         * Formats results as a list of associative arrays,
         * each array containing the key-value pairs for a
         * single row in the results array.
         *
         * Returns PHP array(array(key-value), array(key-value))
         */
        public function resultsToArray($results) {
            $allitems = array();
            foreach ($results as $row) {
                $item = array();
                foreach (array_keys($row) as $key) {
                    if (gettype($key) != "integer") {
                        $item[$key] = $row[$key];
                    }
                }
                array_push($allitems, $item);
            }
            return($allitems);
        }

        public function resultsToJSON($results) {
            return json_encode($this->resultsToArray($results));
        }

    }
?>
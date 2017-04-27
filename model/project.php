<?php
    class Project {
        public $pid;
        public $username;
        public $pname;
        public $description;
        public $post_time;
        public $proj_completed;
        public $completion_time;
        public $minfunds;
        public $maxfunds;
        public $camp_end_time;
        public $camp_finished;
        public $camp_success;

        public function __construct($pid, $username, $pname, $description, $post_time,
                $proj_completed, $completion_time, $minfunds, $maxfunds, $camp_end_time,
                $camp_finished, $camp_success) {
            $this->pid = $pid;
            $this->username = $username;
            $this->pname = $pname;
            $this->description = $description;
            $this->post_time = $post_time;
            $this->proj_completed = $proj_completed;
            $this->completion_time = $completion_time;
            $this->minfunds = $minfunds;
            $this->maxfunds = $maxfunds;
            $this->camp_end_time = $camp_end_time;
            $this->camp_finished = $camp_finished;
            $this->camp_success = $camp_success;
        }

        /**
         * Get single project based on pid.
         */
        public static function getProject($pid) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE pid=:p;";
            $entries = array(":p" => $pid);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $row = $results[0];   //only one project with given pid
                $project = new Project(
                        $row['pid'],
                        $row['username'],
                        $row['pname'],
                        $row['description'],
                        $row['post_time'],
                        $row['proj_completed'],
                        $row['completion_time'],
                        $row['minfunds'],
                        $row['maxfunds'],
                        $row['camp_end_time'],
                        $row['camp_finished'],
                        $row['camp_success']
                    );
                return $project;
            } else {
                return null;
            }
        }

        /**
         * Get a list of project objects for all projects
         * liked by a given user or posted by someone the user follows.
         * Results are sorted by the given field.
         */
        private static function getLikedProjectsByTime($username, $field) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE pid IN "
                    . "(SELECT pid FROM Likes WHERE username=:u) "
                    . "OR pid IN (SELECT pid FROM Project WHERE username IN "
                    . "(SELECT follows FROM Follows WHERE username=:u)) "
                    . "ORDER BY {$field} DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $projects[] = new Project(
                            $row['pid'],
                            $row['username'],
                            $row['pname'],
                            $row['description'],
                            $row['post_time'],
                            $row['proj_completed'],
                            $row['completion_time'],
                            $row['minfunds'],
                            $row['maxfunds'],
                            $row['camp_end_time'],
                            $row['camp_finished'],
                            $row['camp_success']
                        );
                }
                return $projects;
            } else {
                return null;
            }
        }

        /**
         * Get all users that like a given project
         */
        public static function getLikes($pid) {
            $db = DB::getInstance();
            $q = "SELECT username, ltime FROM Likes WHERE pid=:p;";
            $entries = array(":p" => $pid);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $users = [];
                foreach ($results as $row) {
                    $users[$row['username']] = $row['ltime'];
                }
                return $users;
            } else {
                return null;
            }
        }

        /**
         * Get all projects by a certain owner.
         */
        public static function getProjectByUsername($username) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE username=:u;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $projects[] = new Project(
                            $row['pid'],
                            $row['username'],
                            $row['pname'],
                            $row['description'],
                            $row['post_time'],
                            $row['proj_completed'],
                            $row['completion_time'],
                            $row['minfunds'],
                            $row['maxfunds'],
                            $row['camp_end_time'],
                            $row['camp_finished'],
                            $row['camp_success']
                        );
                }
                return $projects;
            } else {
                return null;
            }
        }

        /**
         * Get a list of project details for all projects
         * not liked by a given user and sorted by the given field.
         */
        public static function getNonlikedProjectsByPostTime($username) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE pid NOT IN "
                    . "(SELECT pid FROM Likes WHERE username=:u) "
                    . "ORDER BY post_time DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $projects[] = new Project(
                            $row['pid'],
                            $row['username'],
                            $row['pname'],
                            $row['description'],
                            $row['post_time'],
                            $row['proj_completed'],
                            $row['completion_time'],
                            $row['minfunds'],
                            $row['maxfunds'],
                            $row['camp_end_time'],
                            $row['camp_finished'],
                            $row['camp_success']
                        );
                }
                return $projects;
            } else {
                return null;
            }
        }

        /**
         * Returns all projects a user likes sorted by the post time.
         */
        public static function getLikedProjectsByPostTime($username) {
            return Project::getLikedProjectsByTime($username, 'post_time');
        }

        /**
         * Returns all projects a user likes sorted by the completion time.
         */
        public static function getLikedProjectsByFinishTime($username) {
            return Project::getLikedProjectsByTime($username, 'completion_time');
        }

        /**
         * Get all usernames for people who have donated to a project
         * and have been charged but haven't submitted a rating, yet.
         */
        public static function getRequestedRaters($pid) {
            $db = DB::getInstance();
            $q = "SELECT username FROM Donation WHERE pid=:p AND charged=1 "
                ."AND username NOT IN (SELECT username FROM Rating WHERE pid=:p);";
            $e = [':p' => $pid];
            $results = $db->runSelect($q, $e);
            if ($results) {
                $users = [];
                foreach ($results as $row) {
                    $users[] = $row['username'];
                }
                return $users;
            } else {
                return null;
            }
        }

        /**
         * Gets the average rating for a specific project
         */
        public static function getAverageRating($pid) {
            $db = DB::getInstance();
            $q = "SELECT AVG(rating) AS average FROM Rating WHERE pid=:p;";
            $e = [':p' => $pid];
            $results = $db->runSelect($q, $e);
            if ($results) {
                return $results[0]['average'];
            } else {
                return null;
            }
        }

        /**
         * Gets the list of people who have already rated a
         * specific project
         */
        public static function getRaters($pid) {
            $db = DB::getInstance();
            $q = "SELECT username FROM Rating WHERE pid=:p;";
            $e = [':p' => $pid];
            $results = $db->runSelect($q, $e);
            if ($results) {
                $users = [];
                foreach ($results as $row) {
                    $users[] = $row['username'];
                }
                return $users;
            } else {
                return null;
            }
        }

    }
?>
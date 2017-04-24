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
            $entries = array(":" => $pid);
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
         * Get a list of project details for all projects
         * liked by a given user and sorted by the given field.
         */
        private static function getLikedProjectsByTime($username, $field) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE pid IN "
                    . "(SELECT pid FROM Likes WHERE username=:u) "
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

    }
?>
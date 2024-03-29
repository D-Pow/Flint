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
        public $tags;

        public function __construct($pid, $username, $pname, $description, $post_time,
                $proj_completed, $completion_time, $minfunds, $maxfunds, $camp_end_time,
                $camp_finished, $camp_success, $tags) {
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
            $this->tags = $tags;
        }

        /**
         * Get single project based on pid.
         */
        public static function getProject($pid) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE pid=:p;";
            $entries = array(":p" => $pid);
            $results = $db->runSelect($q, $entries);
            $tags = self::getProjectTags($pid);
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
                        $row['camp_success'],
                        $tags
                    );
                return $project;
            } else {
                return null;
            }
        }

        /**
         * Gets all the tags of a project.
         * Returns an array of tags or an empty array if none.
         */
        public static function getProjectTags($pid) {
            $db = DB::getInstance();
            $q = "SELECT name AS tag_name FROM Project JOIN Ptags USING(pid) "
                ."JOIN Tags USING(tid) WHERE pid=:p;";
            $e = [':p' => $pid];
            $results = $db->runSelect($q, $e);
            $tags = [];
            if ($results) {
                foreach ($results as $row) {
                    $tags[] = $row['tag_name'];
                }
            }
            return $tags;
        }

        /**
         * Gets all projects with a given tag name
         * We only need pid and pname
         */
        public static function getProjectsByTagName($tag_name) {
            $db = DB::getInstance();
            $q = "SELECT pid, pname, description FROM Project JOIN Ptags USING(pid) "
                ."JOIN Tags USING(tid) WHERE name=:tn;";
            $results = $db->runSelect($q, [':tn' => $tag_name]);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $tags = self::getProjectTags($row['pid']);
                    $projects[] = new Project(
                            $row['pid'],
                            null,
                            $row['pname'],
                            $row['description'],
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            $tags
                        );
                }
                return $projects;
            } else {
                return [];
            }
        }

        /**
         * Get a list of project objects for all projects
         * liked by a given user or posted by someone the user follows.
         * Results are sorted by the given field.
         */
        private static function getLikedFollowedProjectsByTime($username, $field) {
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
                    $tags = self::getProjectTags($row['pid']);
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
                            $row['camp_success'],
                            $tags
                        );
                }
                return $projects;
            } else {
                return [];
            }
        }

        /**
         * Get a list of project objects for all projects
         * liked by a given user.
         * Results are sorted by the given field.
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
                    $tags = self::getProjectTags($row['pid']);
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
                            $row['camp_success'],
                            $tags
                        );
                }
                return $projects;
            } else {
                return [];
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
                return [];
            }
        }

        /**
         * Gets the media associated with a project
         */
        public static function getMedia($pid) {
            $db = DB::getInstance();
            $q = "SELECT filename FROM Media JOIN Umedia USING(mid) WHERE uid IN ("
                ."SELECT uid FROM ProjectUpdate WHERE pid=:p);";
            $results = $db->runSelect($q, [':p' => $pid]);
            if ($results) {
                $filenames = [];
                foreach ($results as $row) {
                    $filenames[] = $row['filename'];
                }
                return $filenames;
            } else {
                return [];
            }
        }

        /**
         * Get all projects by a certain owner.
         */
        public static function getProjectsByUsername($username) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Project WHERE username=:u ORDER BY post_time DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $tags = self::getProjectTags($row['pid']);
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
                            $row['camp_success'],
                            $tags
                        );
                }
                return $projects;
            } else {
                return [];
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
                    $tags = self::getProjectTags($row['pid']);
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
                            $row['camp_success'],
                            $tags
                        );
                }
                return $projects;
            } else {
                return [];
            }
        }

        /**
         * Returns all projects a user likes or follows sorted by the post time.
         */
        public static function getLikedFollowedProjectsByPostTime($username) {
            return self::getLikedFollowedProjectsByTime($username, 'post_time');
        }

        /**
         * Returns all projects a user likes or follows sorted by the completion time.
         */
        public static function getLikedFollowedProjectsByFinishTime($username) {
            return self::getLikedFollowedProjectsByTime($username, 'completion_time');
        }

        /**
         * Returns all projects a user likes or follows sorted by the post time.
         */
        public static function getLikedProjectsByPostTime($username) {
            return self::getLikedProjectsByTime($username, 'post_time');
        }

        /**
         * Returns all projects a user likes or follows sorted by the completion time.
         */
        public static function getLikedProjectsByFinishTime($username) {
            return self::getLikedProjectsByTime($username, 'completion_time');
        }

        /**
         * Get all usernames for people who have donated to a project
         * and have been charged but haven't submitted a rating, yet.
         */
        public static function getRequestedRaters($pid) {
            $db = DB::getInstance();
            $q = "SELECT username FROM Donation WHERE pid=:p AND charged=1 "
                ."AND username NOT IN (SELECT username FROM Rating WHERE pid=:p) "
                ."AND (SELECT proj_completed FROM Project WHERE pid=:p)=1;";
            $e = [':p' => $pid];
            $results = $db->runSelect($q, $e);
            if ($results) {
                $users = [];
                foreach ($results as $row) {
                    $users[] = $row['username'];
                }
                return $users;
            } else {
                return [];
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
                return [];
            }
        }

        /**
         * Searches pnames and descriptions for the keyword
         */
        public static function searchProjects($keyword) {
            $db = DB::getInstance();
            //keyword is already lowercase
            $q = "SELECT * FROM Project WHERE lower(pname) LIKE :k OR "
                ."lower(description) LIKE :k;";
            $results = $db->runSelect($q, [':k' => '%'.$keyword.'%']);
            if ($results) {
                $projects = [];
                foreach ($results as $row) {
                    $tags = self::getProjectTags($row['pid']);
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
                            $row['camp_success'],
                            $tags
                        );
                }
                return $projects;
            } else {
                return [];
            }
        }

        /**
         * Get all tags that match a given keyword
         */
        public static function searchTags($keyword) {
            $db = DB::getInstance();
            $q = "SELECT name FROM Tags WHERE lower(name) LIKE :k;";
            $results = $db->runSelect($q, [':k' => '%'.$keyword.'%']);
            if ($results) {
                $tags = [];
                foreach ($results as $row) {
                    $tags[] = $row['name'];
                }
                return $tags;
            } else {
                return [];
            }
        }

    }
?>
<?php
    class Post {
        public $id;
        public $author;
        public $pid;
        public $comment;
        public $ctime;
        public $pname;
        public $owner;  //if the post was made as a project update
        public $mediaNames;

        public function __construct($id, $author, $pid, $comment,
                    $ctime, $pname, $owner, $mediaNames) {
            $this->id = $id;
            $this->author = $author;
            $this->pid = $pid;
            $this->comment = $comment;
            $this->ctime = $ctime;
            $this->pname = $pname;
            $this->owner = $owner;
            $this->mediaNames = $mediaNames;
        }

        /**
         * Gets all the updates for a project ordered by date
         */
        public static function getUpdates($pid) {
            $db = DB::getInstance();
            $q = "SELECT uid, username, pid, comment, ctime FROM ProjectUpdate "
                ."WHERE pid=:p ORDER BY ctime DESC;";
            $results = $db->runSelect($q, [':p' => $pid]);
            if ($results) {
                $posts = [];
                foreach ($results as $row) {
                    $mediaNames = self::getMedia($row['uid']);
                    $posts[] = new Post($row['uid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], null, true, $mediaNames);
                }
                return $posts;
            } else {
                return null;
            }
        }

        /**
         * Gets the media associated with a specific update's uid
         */
        private static function getMedia($uid) {
            $db = DB::getInstance();
            $q = "SELECT filename FROM Media JOIN Umedia USING(mid) WHERE uid=:u;";
            $results = $db->runSelect($q, [':u' => $uid]);
            if ($results) {
                $filenames = [];
                foreach ($results as $row) {
                    $filenames[] = $row['filename'];
                }
                return $filenames;
            } else {
                return null;
            }
        }

        /**
         * Get all comments for a project ordered by date
         */
        public static function getComments($pid) {
            $db = DB::getInstance();
            $q = "SELECT cid, username, pid, comment, ctime FROM Comment "
                ."WHERE pid=:p ORDER BY ctime DESC;";
            $results = $db->runSelect($q, [':p' => $pid]);
            if ($results) {
                $posts = [];
                foreach ($results as $row) {
                    $posts[] = new Post($row['cid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], null, true, null);
                }
                return $posts;
            } else {
                return null;
            }
        }

        /**
         * Get both comments and project updates for all
         * people that the user follows
         */
        public static function getFollowedPosts($username) {
            $updates = Post::getFollowedUpdates($username);
            $comments = Post::getFollowedComments($username);
            //both are non-null
            if ($updates && $comments) {
                $allPosts = array_merge($updates, $comments);
                return $allPosts;
            } else if ($updates) {
                return $updates;
            } else if ($comments) {
                return $comments;
            } else {
                return null;
            }
        }

        /**
         * Get the project updates for those the user follows
         */
        private static function getFollowedUpdates($username) {
            $db = DB::getInstance();
            $q = "SELECT uid, pu.username, pid, comment, ctime, pname FROM ProjectUpdate AS pu "
                    . "JOIN Project USING(pid) WHERE pu.username IN ("
                    . "SELECT follows FROM Follows WHERE username=:u) "
                    . "ORDER BY ctime DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $posts = [];
                foreach ($results as $row) {
                    $mediaNames = self::getMedia($row['uid']);
                    $posts[] = new Post($row['uid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], $row['pname'], true, $mediaNames);
                }
                return $posts;
            } else {
                return null;
            }
        }
    
        /**
         * Get all comments from those the user follows.
         */
        private static function getFollowedComments($username) {
            $db = DB::getInstance();
            $q = "SELECT cid, pu.username, pid, comment, ctime, pname FROM Comment AS pu "
                    . "JOIN Project USING(pid) WHERE pu.username IN ("
                    . "SELECT follows FROM Follows WHERE username=:u) "
                    . "ORDER BY ctime DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $posts = [];
                foreach ($results as $row) {
                    $posts[] = new Post($row['cid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], $row['pname'], false, null);
                }
                return $posts;
            } else {
                return null;
            }
        }

        /**
         * Get both comments and project updates for all
         * people that the user doesn't follow
         */
        public static function getNonfollowedPosts($username) {
            $updates = Post::getNonfollowedUpdates($username);
            $comments = Post::getNonfollowedComments($username);
            //both are non-null
            if ($updates && $comments) {
                $allPosts = array_merge($updates, $comments);
                return $allPosts;
            } else if ($updates) {
                return $updates;
            } else if ($comments) {
                return $comments;
            } else {
                return null;
            }
        }

        /**
         * Get the project updates for those the user doesn't follos
         */
        private static function getNonfollowedUpdates($username) {
            $db = DB::getInstance();
            $q = "SELECT uid, pu.username, pid, comment, ctime, pname FROM ProjectUpdate AS pu "
                    . "JOIN Project USING(pid) WHERE pu.username NOT IN ("
                    . "SELECT follows FROM Follows WHERE username=:u) "
                    . "ORDER BY ctime DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $posts = [];
                foreach ($results as $row) {
                    $mediaNames = self::getMedia($row['uid']);
                    $posts[] = new Post($row['uid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], $row['pname'], true, $mediaNames);
                }
                return $posts;
            } else {
                return null;
            }
        }

        /**
         * Get all comments from those the user doesn't follow.
         */
        private static function getNonfollowedComments($username) {
            $db = DB::getInstance();
            $q = "SELECT cid, pu.username, pid, comment, ctime, pname FROM Comment AS pu "
                    . "JOIN Project USING(pid) WHERE pu.username NOT IN ("
                    . "SELECT follows FROM Follows WHERE username=:u) "
                    . "ORDER BY ctime DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $posts = [];
                foreach ($results as $row) {
                    $posts[] = new Post($row['cid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], $row['pname'], false, null);
                }
                return $posts;
            } else {
                return null;
            }
        }

    }
?>
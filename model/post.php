<?php
    class Post {
        public $id;
        public $author;
        public $pid;
        public $comment;
        public $ctime;
        public $pname;

        public function __construct($id, $author, $pid, $comment, $ctime, $pname) {
            $this->id = $id;
            $this->author = $author;
            $this->pid = $pid;
            $this->comment = $comment;
            $this->ctime = $ctime;
            $this->pname = $pname;
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
                    $posts[] = new Post($row['uid'], $row['username'], $row['pid'],
                        $row['comment'], $row['ctime'], $row['pname']);
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
                        $row['comment'], $row['ctime'], $row['pname']);
                }
                return $posts;
            } else {
                return null;
            }
        }

    }
?>
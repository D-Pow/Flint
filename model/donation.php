<?php
    class Donation {
        public $username;
        public $pid;
        public $amount;
        public $pledge_time;
        public $charged;
        public $charge_date;
        public $pname;

        public function __construct($username, $pid, $amount, $pledge_time,
                $charged, $charge_date, $pname) {
            $this->username = $username;
            $this->pid = $pid;
            $this->amount = $amount;
            $this->pledge_time = $pledge_time;
            $this->charged = $charged;
            $this->charge_date = $charge_date;
            $this->pname = $pname;
        }

        /**
         * Get all donations for those the user follows.
         * Order by pledge_time.
         */
        public static function getFollowedDonations($username) {
            $db = DB::getInstance();
            $q = "SELECT d.username, pid, amount, pledge_time, charged, charge_date, pname "
                    . "FROM Donation AS d JOIN Project USING(pid) WHERE d.username IN ("
                    . "SELECT follows FROM Follows WHERE username=:u) "
                    . "ORDER BY pledge_time DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $donations = [];
                foreach ($results as $row) {
                    $donations[] = new Donation(
                            $row['username'],
                            $row['pid'],
                            $row['amount'],
                            $row['pledge_time'],
                            $row['charged'],
                            $row['charge_date'],
                            $row['pname']
                        );
                }
                return $donations;
            } else {
                return [];
            }
        }

        /**
         * Get all donations for those the user doesn't follow.
         * Order by pledge_time.
         */
        public static function getNonfollowedDonations($username) {
            $db = DB::getInstance();
            $q = "SELECT d.username, pid, amount, pledge_time, charged, charge_date, pname "
                    . "FROM Donation AS d JOIN Project USING(pid) WHERE d.username NOT IN ("
                    . "SELECT follows FROM Follows WHERE username=:u) AND d.username != :u "
                    . "ORDER BY pledge_time DESC;";
            $entries = array(":u" => $username);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $donations = [];
                foreach ($results as $row) {
                    $donations[] = new Donation(
                            $row['username'],
                            $row['pid'],
                            $row['amount'],
                            $row['pledge_time'],
                            $row['charged'],
                            $row['charge_date'],
                            $row['pname']
                        );
                }
                return $donations;
            } else {
                return [];
            }
        }

        /**
         * Gets the total number of donations for a given project.
         */
        public static function getTotalDonations($pid) {
            $db = DB::getInstance();
            $q = "SELECT SUM(amount) AS total FROM Donation WHERE pid=:p GROUP BY pid;";
            $entries = array(":p" => $pid);
            $results = $db->runSelect($q, $entries);
            if ($results) {
                $row = $results[0];
                return $row['total'];
            } else {
                return 0;
            }
        }
    }
?>
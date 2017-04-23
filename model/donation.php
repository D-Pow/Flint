<?php
    class Donation {
        public $username;
        public $pid;
        public $amount;
        public $pledge_time;
        public $charged;
        public $charge_date;

        public function __construct($username, $pid, $amount, $pledge_time,
                $charged, $charge_date) {
            $this->username = $username;
            $this->pid = $pid;
            $this->amount = $amount;
            $this->pledge_time = $pledge_time;
            $this->charged = $charged;
            $this->charge_date = $charge_date;
        }

        /**
         * Get all donations for those the user follows.
         * Order by pledge_time.
         */
        public static function getFollowedDonations($username) {
            $db = DB::getInstance();
            $q = "SELECT * FROM Donation WHERE "
                    . "username IN ("
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
                            $row['charge_date']
                        );
                }
                return $donations;
            } else {
                return null;
            }
        }
    }
?>
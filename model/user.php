<?php
    class User {
        public $username;
        public $uname;
        public $email;
        public $str_addr;
        public $ucity;
        public $ustate;
        public $interests;
        public $ccn;
        public $last_login;

        public function __construct($username, $uname, $email, $str_addr, $ucity,
                $ustate, $interests, $ccn, $last_login) {
            $this->username = $username;
            $this->uname = $uname;
            $this->email = $email;
            $this->str_addr = $str_addr;
            $this->ucity = $ucity;
            $this->ustate = $ustate;
            $this->interests = $interests;
            $this->ccn = $ccn;
            $this->last_login = $last_login;
        }

        public static function getUser($username) {
            $db = DB::getInstance();
            $q = "SELECT * FROM User WHERE username=:u;";
            $results = $db->runSelect($q, array(":u" => $username));
            if ($results) {
                $row = $results[0];  //only one user per username
                $user = new User(
                        $row['username'],
                        $row['uname'],
                        $row['email'],
                        $row['str_addr'],
                        $row['ucity'],
                        $row['ustate'],
                        $row['interests'],
                        $row['ccn'],
                        $row['last_login']
                    );
                return $user;
            } else {
                return null;
            }
        }
    }
?>
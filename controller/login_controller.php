<?php
    class LoginController {

        public function login() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/login/index.html');
        }

        public function createnew() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/login/createaccount.html');
        }
    }
?>
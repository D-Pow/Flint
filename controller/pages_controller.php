<?php
    class PagesController {

        public function home() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/home.php');
        }

        public function error() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/error.php');
        }

        public function user() {

        }

        public function project() {

        }
    }
?>
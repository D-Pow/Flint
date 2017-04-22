<?php
    class PagesController {

        public function home() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/post.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/donation.php');

            $username = $_SESSION['username'];
            $posts = Post::getFollowedPosts($username);
            $projects = Project::getLikedProjects($username);
            $donations = Donation::getFollowedDonations($username);

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
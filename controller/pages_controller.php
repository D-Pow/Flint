<?php
    class PagesController {

        /**
         * Error page
         */
        public function error() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/error_view.php');
        }

        /**
         * Logout page
         */
        public function logout() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/logout_view.php');
        }

        /**
         * Home page
         */
        public function home() {
            if (!isset($_SESSION['username'])) {
                echo "<script>window.location='/Flint/'</script>";
            }

            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/post.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/donation.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/media.php');

            $username = $_SESSION['username'];
            $posts = Post::getFollowedPosts($username);
            //get projects both by post and completion time
            $projects = Project::getLikedProjectsByPostTime($username);
            $compProjects = Project::getLikedProjectsByFinishTime($username);
            $donations = Donation::getFollowedDonations($username);
            //also display non-liked/-followed content
            $nonfollowedDonations = Donation::getNonfollowedDonations($username);
            $nonlikedProjects = Project::getNonlikedProjectsByPostTime($username);
            $nonfollowedPosts = Post::getNonfollowedPosts($username);
            
            //sort content by what came before and after the user's last login time
            $ll = $_SESSION['last_login'];
            $sortedPosts = $this->splitAllItemsAfter($ll, $posts, 'ctime');
            $sortedProjects = $this->splitAllItemsAfter($ll, $projects, 'post_time');
            $sortedCompProjects = $this->splitAllItemsAfter($ll, $compProjects, 
                'completion_time');
            $sortedDonations = $this->splitAllItemsAfter($ll, $donations, 'pledge_time');

            //get any requests for a user to rate a project
            $requestedRatings = [];
            if ($compProjects) {
                foreach ($compProjects as $project) {
                    $requestedUsers = Project::getRequestedRaters($project->pid);
                    //if the user is in the list of those who haven't rated it yet
                    if ($requestedUsers && in_array($username, $requestedUsers)) {
                        $requestedRatings[] = $project;
                    }
                }
            }

            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/home_view.php');
        }

        /**
         * User profile page
         */
        public function user() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/user.php');
            $username = $_GET['user'];
            $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); //sanitize
            $user = User::getUser($username);
            if ($user) {
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
                //get owned projects
                $ownedProjects = Project::getProjectsByUsername($username);
                //get liked projects
                $likedProjects = Project::getLikedProjectsByPostTime($username);
                //get recently viewed projects and tags
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/view_log.php');
                $viewedProjects = ViewLog::getRecentProjectViews($username);
                $viewedTags = ViewLog::getRecentTagViews($username);
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/user_view.php');
            } else {
                $this->error();
            }
        }

        /**
         * Project page
         */
        public function project() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            $pid = $_GET['pid'];
            $pid = htmlspecialchars($pid, ENT_QUOTES, 'UTF-8'); //sanitize
            $pid = intval($pid);  //only allow ints
            $project = Project::getProject($pid);
            $likes = Project::getLikes($pid);
            //log project view
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/view_log.php');
            ViewLog::logProjectView($_SESSION['username'], $pid);
            //get comments and updates for a project
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/post.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/media.php');
            $updates = Post::getUpdates($pid);
            $comments = Post::getComments($pid);
            if ($project) {
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/donation.php');
                $totalFunds = Donation::getTotalDonations($pid);
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/project_view.php');
            } else {
                $this->error();
            }
        }

        public function posted_projects() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            $projects = Project::getProjectsByUsername($_SESSION['username']);
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/user_projects.php');
        }

        /**
         * New project page
         */
        public function new() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/new_project_view.php');
        }

        /**
         * Rate project page
         */
        public function rate() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            $pid = $_GET['pid'];
            $pid = htmlspecialchars($pid, ENT_QUOTES, 'UTF-8');
            $pid = intval($pid);
            $project = Project::getProject($pid);
            if ($project) {
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/donation.php');
                $totalFunds = Donation::getTotalDonations($pid);
                $likes = Project::getLikes($pid);
                require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/rate_project.php');
            } else {
                $this->error();
            }
        }

        /**
         * Display a tag page that has all projects with a given tag name
         */
        public function tag() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            $tag_name = $_GET['tag'];
            $tag_name = htmlspecialchars($tag_name, ENT_QUOTES, 'UTF-8');
            //log tag view
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/view_log.php');
            ViewLog::logTagView($_SESSION['username'], $tag_name);
            //get projects matching that tag
            $projects = Project::getProjectsByTagName($tag_name);
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/tag_view.php');
        }

        /**
         * Display the search-results page
         */
        public function search() {
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/post.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/project.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/user.php');
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/model/view_log.php');
            $keyword = $_GET['q'];
            $keyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');
            $keyword = strtolower($keyword); //make lowercase for ease of searching
            //log that the user searched for that keyword
            ViewLog::logSearch($_SESSION['username'], $keyword);
            $projects = Project::searchProjects($keyword);
            $users = User::searchUsers($keyword);
            $tags = Project::searchTags($keyword);
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/pages/search_view.php');
        }

        /**
         * Gets all items (posts, projects, etc.) from the itemArray
         * whose field specified by $field come after the $date
         * 
         * Returns array of two items:
         *   array[0] those after the date,
         *   array[1] those before the date
         */
        private function splitAllItemsAfter($date, $itemArray, $field) {
            if ($itemArray == null) {
                return null;
            }
            $d = date($date);
            $after = null;
            $before = null;
            for ($i = 0; $i < count($itemArray); $i++) {
                $item = $itemArray[$i];
                $itemDate = date($item->$field);
                //keep cycling through itemArray until you find a date
                //that comes before $date
                if ($this->isAfter($d, $itemDate)) {
                    //quit searching through array and split it in two
                    $after = array_slice($itemArray, 0, $i);
                    $before = array_slice($itemArray, $i);
                    break;
                }
            }
            return [$after, $before];
        }

        /**
         * Checks if date1 comes after date2.
         * Meant to compare a database field, date1
         * with the last_login, date2.
         * 
         * $date1 and $date2 are strings
         *
         * If date1 is null, returns false.
         */
        private function isAfter($date1, $date2) {
            if ($date1 == null) {
                return false;
            }

            $d1 = date($date1);
            $d2 = date($date2);
            if ($d1 > $d2) {
                return true;
            } else {
                return false;
            }
        }
    }
?>
<link rel="stylesheet" href="/Flint/view/pages/css/home.css" />

<?php
    if (!isset($_SESSION['username'])) {
        //user isn't logged in
        ?>
        <script>window.location="/Flint/"</script>
        <?php
    }

    /**
     * Display a request to the user to rate a project
     */
    function displayRatingRequest($project) {
        $html = "
            <div class='entry'>
                <h4>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $project->pid
                        . "'>"
                        . $project->pname ."
                    </a>
                    has been completed! Please rate this project 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=rate&pid="
                        . $project->pid
                        . "'>here.
                    </a>
                </h4>
            </div>
            ";
        return $html;
    }

    /**
     * Gets the appropriate project content and converts
     * it to a string containing the appropriate html
     */
    function displayProject($project) {
        $html = "
            <div class='entry'>
                <h4>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&user="
                        . $project->username
                        . "'>"
                        . $project->username . "
                    </a>
                    posted a project: 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $project->pid
                        . "'>"
                        . $project->pname ."
                    </a>
                    <br />
                    ("
                    . date('h:i A, m-d-Y', strtotime($project->post_time)).")
                </h4>
                <p>"
                . $project->description . "
                </p>
            </div>
            ";
        return $html;
    }

    /**
     * Gets the appropriate donation content and converts
     * it to a string containing the appropriate html
     */
    function displayDonation($donation) {
        $html = "
            <div class='entry'>
                <br />
                <p>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&user="
                        . $donation->username
                        . "'>"
                        . $donation->username . "
                    </a>
                    pledged $"
                    . $donation->amount
                    . " towards 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        .  $donation->pid
                        . "'>"
                        . $donation->pname . "
                    </a> 
                    on
                </p>
                <p>"
                    . date('h:i A, m-d-Y', strtotime($donation->pledge_time)).".
                </p>
            </div>
            ";
        return $html;
    }

    /**
     * Gets the appropriate post content and converts
     * it to a string containing the appropriate html
     */
    function displayPost($post) {
        $html = "
            <div class='entry'>
                <h4>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&user="
                        . $post->author
                        . "'>"
                        . $post->author . "
                    </a>"
                    . (
                        ($post->owner)
                        ? 'made an update on '
                        : 'commented on'
                      ) . "
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        .  $post->pid
                        . "'>" . $post->pname . "
                    </a>
                    <br />("
                        . date('h:i A, m-d-Y', strtotime($post->ctime)).")
                </h4>
                <p>"
                . $post->comment . "
                </p>
            </div>
            ";
        return $html;
    }

    //display rating requests first
    if ($requestedRatings) {
        foreach ($requestedRatings as $project) {
            echo displayRatingRequest($project);
        }
    }

    //arrays will hold the html to display and will be shuffled
    //so that different types of content are mixed together

    //display new content before old content
    $newContent = [];

    //projects
    if ($sortedProjects[0]) {
        foreach ($sortedProjects[0] as $project) {
            $newContent[] = displayProject($project);
        }
    }
    
    //donations
    if ($sortedDonations[0]) {
        foreach ($sortedDonations[0] as $donation) {
            $newContent[] = displayDonation($donation);
        }
    }

    //posts
    if ($sortedPosts[0]) {
        foreach ($sortedPosts[0] as $post) {
            $newContent[] = displayPost($post);
        }
    }

    shuffle($newContent);
    foreach ($newContent as $html) {
        echo $html;
    }

    //print last login time
    echo "<h3 class='divider'>
            -----------------last login: "
            . date('h:i A, m-d-Y', strtotime($_SESSION['last_login'])) .
            "-----------------
          </h3>";

    //everything that happended before the last login
    $oldContent = [];

    //projects
    if ($sortedProjects[1]) {
        foreach ($sortedProjects[1] as $project) {
            $oldContent[] = displayProject($project);
        }
    }
    
    //donations
    if ($sortedDonations[1]) {
        foreach ($sortedDonations[1] as $donation) {
            $oldContent[] = displayDonation($donation);
        }
    }

    //posts
    if ($sortedPosts[1]) {
        foreach ($sortedPosts[1] as $post) {
            $oldContent[] = displayPost($post);
        }
    }

    shuffle($oldContent);
    foreach ($oldContent as $html) {
        echo $html;
    }

    //separate non-followed content from followed content
    echo "<h3 class='divider'>
            ------------------------non-followed content------------------------
          </h3>";

    //display non-followed content, too
    $nonfollowedContent = [];

    //projects
    if ($nonlikedProjects) {
        foreach($nonlikedProjects as $project) {
            $nonfollowedContent[] = displayProject($project);
        }
    }
    
    //donations
    if ($nonfollowedDonations) {
        foreach($nonfollowedDonations as $donation) {
            $nonfollowedContent[] = displayDonation($donation);
        }
    }

    //posts
    if ($nonfollowedPosts) {
        foreach($nonfollowedPosts as $post) {
            $nonfollowedContent[] = displayPost($post);
        }
    }

    shuffle($nonfollowedContent);
    foreach ($nonfollowedContent as $html) {
        echo $html;
    }

?>
<link rel="stylesheet" href="/Flint/view/pages/css/project.css" />

<div id="container">
    <?php
        $owner = ($_SESSION['username'] == $project->username);
        //if owner, allow them to change description
        if ($owner) {
            echo "<h1>Title: "
                ."<input id='title' type='text' value='".$project->pname."'>"
                ."</h1>";
        } else {
            //otherwise, just output the title
            echo "<h1>".$project->pname."</h1>";
        }
    ?>
    <br />

    <h3>Author:</h3>
    <a class='entry-title' href='/Flint/?controller=pages&action=user&user=<?php
            echo $project->username; ?>'><?php echo $project->username; ?></a>

    <h2>Posted:</h2>
    <p><?php echo $project->post_time; ?></p>
    
    <?php if ($project->proj_completed) {
            echo "<h3>Project completed: ".$project->completion_time."</h3>";
        }?>
    <br />
    
    <h3>Description:</h3>
    <?php
        //if owner, allow them to change description
        if ($owner) {
            echo "<textarea id='description' type='text' rows='10' cols='50'>"
                .$project->description."</textarea>";
        } else {
            //otherwise, just output the description
            echo "<p>".$project->description."</p>";
        }
    ?>
    <br />
    
    <h3>Funding:</h3>
    <p>Min: <?php echo $project->minfunds; ?><input id='donation-scale' type='range'
            min='<?php echo $project->minfunds; ?>'
            max='<?php echo $project->maxfunds; ?>'
            value='<?php echo $totalFunds ? $totalFunds : 0; ?>' disabled>
        Max: <?php echo $project->maxfunds; ?>
    </p>
    <p id='current-funds'>
        Current funds: <?php echo $totalFunds ? $totalFunds : 0; ?></p>
    
    <?php
    if ($project->camp_finished) {
        echo "<h2>The campaign was "
            .($project->camp_success ? "" : "not ")."a success.</h2>";
    } else if ($project->username != $_SESSION['username']) {
        //allow non-owners to donate

        //load donation options
        ?>
        <h3>Would you be willing to donate?</h3>
        <input type='number' name='donation' id='donation' step='1' 
                title="Whole numbers" pattern="[0-9]">
        <button type='button' id='submit'
                onclick='donate(<?php echo $project->pid; ?>)'>Donate</button>
        <p id='reply'></p>
        <?php
    }
    if (($project->username != $_SESSION['username'] && $likes
            && !array_key_exists($_SESSION['username'], $likes))
        //no likes yet
        || ($project->username != $_SESSION['username'] && !$likes)) {
        //allow non-owners to like the project if they haven't liked it already
        ?>
        <button type='button' id='like-button'
                onclick='like(<?php echo $project->pid; ?>)'>Like</button>
        <?php
    }
    //output how many people like the project
    echo "<p id='likes'>".count($likes)." likes</p>";
    echo "<br />";
    if ($owner) {
        //allow owner to change project tags
        ?>
        <h3 style='margin: 0;'>Tags: </h3>
        <p style='margin: 0;'>(Please separate tags using commas)</p>
        <?php
        $tags = $project->tags;
        $str = "";
        for ($i = 0; $i < count($tags); $i++) {
            $str .= $tags[$i].($i+1 == count($tags) ? "" : ", ");
        }
        echo "<input type='text' id='tags-input' value='{$str}'><br />";
        echo "<button id='save' onclick='saveChanges("
                .$project->pid.")'>Save Changes</button>";
    } else {
        echo getTags($project);
    }
    //give ability to comment/update even if the project is complete
    //note that this means people can comment on failed project in order
    //to give feedback as to why the project failed or to encourage the
    //owner to try again
    if ($owner) {
        echo "<h2 id='post-title'>Post an update: </h2>";
    } else {
        echo "<h2 id='post-title'>Post a comment: </h2>";
    }
    ?>
    <input type='text' id='post-content'>
    <br />
    <button type='button' id='post-button'
       onclick='post(
        <?php echo $project->pid.','.intval($owner).',"'.$_SESSION['username'].'"'; 
        ?>)'>Post</button>
    <p id='post-thanks'></p>
    <?php
?>
</div>
<script src='/Flint/view/pages/js/project.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<?php
    //display updates and comments, sorted first by update vs comment,
    //then by post time
    foreach (displayPosts($updates, $comments) as $post) {
        echo $post;
    }

    /**
     * Outputs the html as a string in order to display 
     * comments and updates properly
     */
    function displayPosts($updates, $comments) {
        $posts = [];
        if ($updates || $comments) {
            //print divider for posts if they exist
            echo "<h3 class='divider'>
                    ----------------------------------Posts----------------------------------
                  </h3>";
        }
        if ($updates) {
            foreach ($updates as $update) {
                $html = "
                    <div class='entry update-post'>
                        <h4>
                            <a class='entry-title' 
                                href='/Flint/?controller=pages&action=user&user="
                                . $update->author
                                . "'>"
                                . $update->author . "
                            </a>
                            posted an update ("
                            . date('h:i A, m-d-Y', strtotime($update->ctime)).")
                        </h4>
                        <p>"
                        . $update->comment . "
                        </p>
                    </div>
                ";
                $posts[] = $html;
            }
        }
        if ($comments) {
            foreach ($comments as $comment) {
                $html = "
                    <div class='entry comment-post'>
                        <h4>
                            <a class='entry-title' 
                                href='/Flint/?controller=pages&action=user&user="
                                . $comment->author
                                . "'>"
                                . $comment->author . "
                            </a>
                            ("
                            . date('h:i A, m-d-Y', strtotime($comment->ctime)).")
                        </h4>
                        <p>"
                        . $comment->comment . "
                        </p>
                    </div>
                ";
                $posts[] = $html;
            }
        }
        return $posts;
    }

    /**
     * Gets the tags for a given project
     */
    function getTags($project) {
        $str = "";
        if ($project->tags) {
            $str .= "<br /><h3 style='margin: 0'>Tags: </h3><p>";
            $length = count($project->tags);
            for ($i = 0; $i < $length; $i++) {
                $tag_name = $project->tags[$i];
                //only put comma if it's not the last tag
                $end = $i+1 == $length ? "" : ", ";
                $content = "<a class='entry-title' "
                        ."href='/Flint/?controller=pages&action=tag&tag="
                        .$tag_name."'>".$tag_name."</a>";
                $str .= $content.$end;
            }
            $str .= "</p>";
        }
        return $str;
    }
?>
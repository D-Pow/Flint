<link rel='stylesheet' href='/Flint/view/pages/css/user.css'>

<div id="container">
    <h1><?php echo $user->username; ?></h1>
    <hr />
    <h3>Name:</h3>
    <p><?php echo $user->uname; ?></p>
    <br />
    <?php
        if ($_SESSION['username'] == $username) {
            //owner can edit their own pages
            ?>
            <h3>Email:</h3>
            <input id='email' type='text' value='<?php echo $user->email; ?>'>
            <br />
            <h3>Street Address:</h3>
            <input id='str-addr' type='text' value='<?php echo $user->str_addr; ?>'>
            <br />
            <h3>City:</h3>
            <input id='city' type='text' value='<?php echo $user->ucity; ?>'>
            <br />
            <h3>State:</h3>
            <input id='state' type='text' value='<?php echo $user->ustate; ?>'>
            <br />
            <h3>Interests:</h3>
            <textarea id='interests' rows='10' cols='30'><?php echo $user->interests; ?></textarea>
            <br />
            <button id='save-button' type='button'
                onclick='saveChanges()'>Save Changes</button>
            <script src='/Flint/view/pages/js/user.js'></script>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js">
            </script>
            <br />
            <br />
            <?php
        }
    ?>
    <br />
<?php
    //display projects owned by this user
    if ($ownedProjects) {
        echo "<h2>Projects Posted by ".$username."</h2>";
        echo "<ul>";
        foreach($ownedProjects as $project) {
            $html = "<a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $project->pid
                        . "'>"
                        . $project->pname ."
                    </a>
            ";
            echo "<li>" . $html . "</li>";
        }
        echo "</ul>";
    }

    //display liked projects
    if ($likedProjects) {
        echo "<h2>Projects that ".$username." likes</h2>";
        echo "<ul>";
        foreach($likedProjects as $project) {
            $html = "<a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $project->pid
                        . "'>"
                        . $project->pname ."
                    </a>
            ";
            echo "<li>" . $html . "</li>";
        }
        echo "</ul>";
    }

    //display recent projects and tags viewed by this user
    if ($viewedProjects) {
        echo "<h2>Projects recently viewed by ".$username."</h2>";
        echo "<ul>";
        foreach(array_keys($viewedProjects) as $pname) {
            $html = "<a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $viewedProjects[$pname]
                        . "'>"
                        . $pname ."
                    </a>
            ";
            echo "<li>" . $html . "</li>";
        }
        echo "</ul>";
    }
    if ($viewedTags) {
        echo "<h2>Tags recently viewed by ".$username."</h2>";
        echo "<ul>";
        foreach($viewedTags as $tag_name) {
            $html = "<a class='entry-title' 
                        href='/Flint/?controller=pages&action=tag&tag="
                        . $tag_name
                        . "'>"
                        . $tag_name ."
                    </a>
            ";
            echo "<li>" . $html . "</li>";
        }
        echo "</ul>";
    }
?>
</div>
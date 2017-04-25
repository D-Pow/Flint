<div id="container">
    <h1><?php echo $user->username; ?></h1>
    <br />
    <h3>Name:</h3>
    <p><?php echo $user->uname; ?></p>
    <br />
    <h3>City:</h3>
    <p><?php echo $user->ucity; ?></p>
    <br />
    <?php if ($user->interests) {
            echo "<h3>Interests:</h3>";
            echo "<p>".$user->interests."</p><br />";
        }?>
<?php
    if ($projects) {
        echo "<h2>Liked Projects</h2>";
        echo "<ul>";
        foreach($projects as $project) {
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
?>
</div>
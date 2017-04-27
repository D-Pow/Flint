<link rel="stylesheet" href="/Flint/view/pages/css/project.css" />

<div id="container">
    <?php
        //if owner, allow them to change description
        if ($_SESSION['username'] == $project->username) {
            echo "<script src='/Flint/view/pages/js/project_update.js'></script>";
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
        if ($_SESSION['username'] == $project->username) {
            echo "<script src='/Flint/view/pages/js/project_update.js'></script>";
            echo "<textarea id='description' type='text' rows='10' cols='50'>"
                .$project->description."</textarea>";
            echo "<button id='save' onclick='saveChanges("
                .$project->pid.")'>Save Changes</button>";
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
        <script src='/Flint/view/pages/js/donate.js'></script>
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
        <script src='/Flint/view/pages/js/project_like.js'></script>
        <?php
    }
    //output how many people like the project
    echo "<p id='likes'>".count($likes)." likes</p>";
?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" href="/Flint/view/pages/project.css" />

<div id="container">
    <h1><?php echo $project->pname; ?></h1>
    <br />

    <h3>Author:</h3>
    <a class='entry-title' href='/Flint/?controller=pages&action=user&user=<?php echo $project->username; ?>'><?php echo $project->username; ?></a>

    <h2>Posted:</h2>
    <p><?php echo $project->post_time; ?></p>
    
    <?php if ($project->proj_completed) {
            echo "<h3>Project completed: ".$project->completion_time."</h3>";
        }?>
    <br />
    
    <h3>Description:</h3>
    <p><?php echo $project->description; ?></p>
    <br />
    
    <h3>Funding:</h3>
    <p>Min: <?php echo $project->minfunds; ?><input type='range' min='<?php echo $project->minfunds; ?>'
            max='<?php echo $project->maxfunds; ?>'
            value='<?php echo $totalFunds; ?>' disabled>
        Max: <?php echo $project->maxfunds; ?>
    </p>
    <p>Current funds: <?php echo $totalFunds; ?></p>
    
    <?php if ($project->camp_finished) {
        echo "<h2>The campaign was ".($project->camp_success ? "" : "not ")."a success.</h2>";
    } else {
        //load donation options
        ?>
        <h3>Would you be willing to donate?</h3>
        <input type='number' name='donation' id='donation' step='1' 
                title="Whole numbers" pattern="[0-9]">
        <button type='button' id='submit'
                onclick='donate(<?php echo $project->pid; ?>)'>Donate</button>
        <p id='reply'></p>
        <script src='/Flint/view/pages/donate.js'></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <?php
    }
?>
</div>
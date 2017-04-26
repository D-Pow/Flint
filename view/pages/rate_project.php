<link rel="stylesheet" href="/Flint/view/pages/css/project.css" />

<div id="container">
    <h1><?php echo $project->pname; ?></h1>
    <br />

    <h3>Author:</h3>
    <a class='entry-title' href='/Flint/?controller=pages&action=user&user=<?php
            echo $project->username; ?>'><?php echo $project->username; ?></a>

    <h2>Posted:</h2>
    <p><?php echo $project->post_time; ?></p>
    
    <h3>Project completed: <?php echo $project->completion_time; ?></h3>
    <br />
    
    <h3>Description:</h3>
    <p><?php echo $project->description; ?></p>
    <br />
    
    <h3>Funding:</h3>
    <p>Min: <?php echo $project->minfunds; ?><input id='donation-scale' type='range'
            min='<?php echo $project->minfunds; ?>'
            max='<?php echo $project->maxfunds; ?>'
            value='<?php echo $totalFunds ? $totalFunds : 0; ?>' disabled>
        Max: <?php echo $project->maxfunds; ?>
    </p>
    <p id='current-funds'>
        Final funds: <?php echo $totalFunds ? $totalFunds : 0; ?></p>
    <br />
    <h2>The campaign was a success!</h2>
    <p id='likes'>Total likes: <?php echo count($likes); ?></p>

    <br />
    <h2>Please rate the project: </h2>
    <input type='radio' name='rating' value='5'>Excellent<br />
    <input type='radio' name='rating' value='4'>Good<br />
    <input type='radio' name='rating' value='3'>Fair<br />
    <input type='radio' name='rating' value='2'>Poor<br />
    <input type='radio' name='rating' value='1'>Horrible<br />
    <button type='button' onclick='rate(<?php echo $project->pid; ?>)'>Submit</button>
    <script src='/Flint/view/pages/js/rate.js'></script>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
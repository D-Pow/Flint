<?php
    if (!isset($_SESSION)) {
        //if session hasn't started, user went to home via direct path
        ?>
        <script>window.location="/Flint/?controller=pages&action=home"</script>
        <?php
    }
    if (!isset($_SESSION['username'])) {
        ?>
        <script>window.location="/Flint/"</script>
        <?php
    }

    //display posts, projects, and donations from those the user follows
    //include null checks for posts, projects, and donations
?>
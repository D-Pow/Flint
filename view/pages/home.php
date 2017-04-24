<link rel="stylesheet" href="/Flint/view/pages/home.css" />

<?php
    if (!isset($_SESSION['username'])) {
        //user isn't logged in
        ?>
        <script>window.location="/Flint/"</script>
        <?php
    }

    //projects should be listed by most recent date, and will be sorted
    //by being before/after the last_login time
    //$sortedProjects
    //$sortedCompProjects

?>

<!--Header content-->
<h2 id='user-title'>Welcome <?php echo $username; ?>!</h2>
<br /><br />
<a id='edit-profile' href='/Flint/?controller=pages&action=user&author=<?php 
    echo $username; ?>'>Profile</a>

<?php
    
    //donations
    if ($sortedDonations[1]) {
        foreach ($sortedDonations[1] as $donation) {
            //output html
            ?>
            <div class='entry'>
                <br />
                <p>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&author=<?php 
                        echo $donation->username; ?>'>
                        <?php echo $donation->username; ?>
                    </a>
                    pledged $<?php echo $donation->amount; ?> towards 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid=<?php 
                        echo $donation->pid; ?>'>
                    <?php echo $donation->pname ?>
                    </a>
                    .
                </p>
            </div>
            <?php
        }
    }

    //posts
    if ($sortedPosts[1]) {
        foreach ($sortedPosts[1] as $post) {
            //output html
            ?>
            <div class='entry'>
                <h4>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&author=<?php 
                        echo $post->author; ?>'>
                        <?php echo $post->author; ?>
                    </a>
                    > 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid=<?php 
                        echo $post->pid; ?>'>
                        <?php echo
                            ($post->owner)
                            ? $post->pname . ' (Update)'
                            : $post->pname; 
                        ?>
                    </a>
                </h4>
                <p><?php echo $post->comment; ?></p>
            </div>
            <?php
        }
    }

?>
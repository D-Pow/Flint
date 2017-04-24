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
    //$sortedPosts
    //$sortedProjects
    //$sortedCompProjects
    //$sortedDonations

    if ($sortedPosts[1]) {
        foreach ($sortedPosts[1] as $post) {
            //output html
            ?>
            <div class='entry'>
                <h4>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&author=<?php 
                        echo $post->author; ?>'><?php echo $post->author; ?>
                    </a>
                    > 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid=<?php 
                        echo $post->pid; ?>'><?php echo $post->pname; ?>
                    </a>
                </h4>
                <p><?php echo $post->comment; ?></p>
            </div>
            <?php
        }
    }

?>
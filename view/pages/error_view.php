<p class='error'>Sorry, that page is an error.<p>
<p class='error'>Click 
<?php
    //output right redirection link
    if (isset($_SESSION['username'])) {
        echo "<a class='button' href='/Flint/?controller=pages&action=home'>here</a>";
    } else {
        echo "<a class='button' href='/Flint/'>here</a>";
    }
?>
 to return.</p>
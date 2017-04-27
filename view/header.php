<!--Header content-->
<div id='header'>
<h2 id='user-title'>Welcome <?php echo $_SESSION['username']; ?>!</h2>
<br /><br />
<ul id='nav'>
    <li>
        <a class='button' id='home'
            href='/Flint/?controller=pages&action=home'>Home</a>
    </li>
    <li>
        <a class='button' id='posted-projects'
            href='/Flint/?controller=pages&action=posted_projects'>My Projects</a>
    </li>
    <li>
        <a class='button' id='new-project'
            href='/Flint/?controller=pages&action=new'>New Project</a>
    </li>
    <li>
        <a class='button' id='edit-profile'
            href='/Flint/?controller=pages&action=user&user=<?php 
            echo $_SESSION['username']; ?>'>Profile</a>
    </li>
    <li>
        <a class='button' id='logout'
            href='/Flint/?controller=pages&action=logout'>Logout</a>
    </li>
</ul>
</div>
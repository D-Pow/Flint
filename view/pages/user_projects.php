<link rel="stylesheet" href="/Flint/view/pages/css/home.css" />

<?php
    if (!isset($_SESSION['username'])) {
        //user isn't logged in
        ?>
        <script>window.location="/Flint/"</script>
        <?php
    }
    
    /**
     * Gets the appropriate project content and converts
     * it to a string containing the appropriate html
     */
    function displayProject($project) {
        $html = "
            <div class='entry'>
                <h4>
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $project->pid
                        . "'>"
                        . $project->pname ."
                    </a>
                </h4>
                <p>"
                . $project->description . "
                </p>
            </div>
            ";
        return $html;
    }

    if ($projects) {
        foreach($projects as $project) {
            echo displayProject($project);
        }
    } else {
        ?>
        <h2 style='text-align: center;'>You haven't posted any projects yet!</h2>
        <h3 style='text-align: center;'>
            <a class='button' href='/Flint/?controller=pages&action=new'>Create one now!</a></h3>
        <?php
    }

?>
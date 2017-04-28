<div id="container">
    <h2>Tag: <?php echo $tag_name; ?></h2>
</div>
<?php
    if ($projects) {
        foreach ($projects as $project) {
            echo displayProject($project);
        }
    }

    /**
     * Outputs the appropriate html for a project
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
                </p>".getTags($project)."
            </div>
            ";
        return $html;
    }

    /**
     * Gets the html to display a project's tags
     */
    function getTags($project) {
        $str = "";
        if ($project->tags) {
            $str .= "<br /><h4 style='margin: 0'>Tags: </h4><p>";
            $length = count($project->tags);
            for ($i = 0; $i < $length; $i++) {
                $tag_name = $project->tags[$i];
                //only put comma if it's not the last tag
                $end = $i+1 == $length ? "" : ", ";
                $content = "<a class='entry-title' "
                        ."href='/Flint/?controller=pages&action=tag&tag="
                        .$tag_name."'>".$tag_name."</a>";
                $str .= $content.$end;
            }
            $str .= "</p>";
        }
        return $str;
    }
?>
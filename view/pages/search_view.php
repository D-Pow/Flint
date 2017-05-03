<?php
    
    //get all results
    $results = [];

    if ($projects) {
        foreach ($projects as $project) {
            $results[] = displayProject($project);
        }
    }

    if ($users) {
        foreach ($users as $user) {
            $results[] = displayUser($user);
        }
    }

    if ($tags) {
        foreach ($tags as $tag) {
            $results[] = displayTag($tag);
        }
    }
    
    foreach ($results as $html) {
        echo $html;
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
                        href='/Flint/?controller=pages&action=user&user="
                        . $project->username
                        . "'>"
                        . $project->username . "
                    </a>
                    posted a project: 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=project&pid="
                        . $project->pid
                        . "'>"
                        . $project->pname ."
                    </a>
                    <br />
                    ("
                    . date('h:i A, m-d-Y', strtotime($project->post_time)).")
                </h4>
                <p>"
                . $project->description . "
                </p>".getTags($project)."
            </div>
            ";
        return $html;
    }

    /**
     * Gets the tags for a given project
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

    /**
     * Gets the html to display users
     */
    function displayUser($user) {
        $html = "
            <div class='entry'>
                <h4>User: 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=user&user="
                        . $user->username
                        . "'>"
                        . $user->username . "
                    </a>
                </h4>
            </div>
            ";
        return $html;
    }

    /**
     * Gets the html to display tags
     */
    function displayTag($tag) {
        $html = "
            <div class='entry'>
                <h4>Tag: 
                    <a class='entry-title' 
                        href='/Flint/?controller=pages&action=tag&tag="
                        . $tag
                        . "'>"
                        . $tag . "
                    </a>
                </h4>
            </div>
            ";
        return $html;
    }

?>
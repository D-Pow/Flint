<?php
    
    $controllers = ['login' => ['login','createnew'],
                    'pages' => ['error','logout','home','user',
                                'post','project', 'new','rate',
                                'posted_projects', 'tag']];

    if (array_key_exists($controller, $controllers)) {
        if (in_array($action, $controllers[$controller])) {
            route($controller, $action);
        } else {
            route('pages', 'error');
        }
    } else {
        route('pages', 'error');
    }


    function route($controller, $action) {
        require($_SERVER['DOCUMENT_ROOT'].'/Flint/controller/' . $controller . "_controller.php");

        //Call the action method from the controller class
        $class = ucfirst($controller)."Controller";
        $cont = new $class;
        $cont->$action();
    }

?>
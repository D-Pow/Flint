<?php
    require_once('db.php');

    session_start();
    if (isset($_GET['controller']) && isset($_GET['action'])) {
        $controller = $_GET['controller'];
        $action = $_GET['action'];
    } else {
        $controller = 'login';
        $action = 'login';
    }

    require_once('view/layout.php');
?>
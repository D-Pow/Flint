<!DOCTYPE html>
<html>
<head>
    <!--Force browser to pull page from server-->
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <link rel="stylesheet" href="/Flint/view/common.css" />
    <title>Flint</title>
</head>
<body>
    <?php
        if (isset($_SESSION['username']) && $controller != 'login'
            && $action != 'logout') {
            //set header if logged in
            require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/view/header.php');
        }

        //call correct controller
        require_once($_SERVER['DOCUMENT_ROOT'].'/Flint/route.php');
    ?>
</body>
</html>
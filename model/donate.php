<?php
    if (!isset($_POST['donation']) || $_POST['donation'] == '' ||
        $_POST['username'] == '') {
        reply("no values");
    }
    
?>
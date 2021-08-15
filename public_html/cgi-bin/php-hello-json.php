<?php
    header("Cache-Control: no-cache");
    header("Content-type: application/json");

    $message = ('message' => 'Hello, PHP!', 'date' => date(), 'cuurentIP' => $_SERVER['REMOTE_ADDR']);
    
    $json = json_encode($message);
    echo $json
?>
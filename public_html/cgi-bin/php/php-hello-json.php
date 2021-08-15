<?php
    header("Cache-Control: no-cache");
    header("Content-type: application/json");

    $date = date("D M j H:i:s Y");

    $message = array('message' => 'Hello, PHP!', 'date' => $date, 'currentIP' => $_SERVER['REMOTE_ADDR']);
    
    $json = json_encode($message);
    echo $json
?>
<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
    session_start();
    $_SESSION = [];
    session_destroy();
?>
<html>
    <head>
        <title>PHP Session Destroyed</title>
    </head>
    <body>
        <h1 align=center>PHP Session Destroyed</h1>
        <hr/>
        <a href="/cgi-bin/php/php-sessions-1.php">Back to Page 1</a><br/>
        <a href="/cgi-bin/php/php-sessions-2.php">Back to Page 2</a><br/>
        <a href="/hw2/php-cgiform.html">PHP CGI Form</a>
    </body>
</html>
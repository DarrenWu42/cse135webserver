<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
    unset($_SESSION['name']);
?>
<html>
    <head>
        <title>PHP Session Destroyed</title>
    </head>
    <body>
        <h1>PHP Session Destroyed</h1>
        <a href="/cgi-bin/php/php-sessions-1.php">Back to Page 1</a><br/>
        <a href="/cgi-bin/php/php-sessions-2.php">Back to Page 2</a><br/>
        <a href="/hw2/php-cgiform.html">PHP CGI Form</a>
    </body>
</html>
<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
    session_start();
?>
<html>
    <head>
        <title>PHP Sessions</title>
    </head>
    <body>
        <h1>PHP Sessions Page 2</h1>
        <p>
            <b>Name:</b> <?php echo(array_key_exists('name', $_SESSION) ? $_SESSION['name'] : "None")?>
            
            <a href="/cgi-bin/php/php-sessions-1.php">Session Page 1</a>
            <a href="/hw2/php-cgiform.html">PHP CGI Form</a>
        </p>
        <form style="margin-top:30px" action="/cgi-bin/php/php-destroy-session.php" method="get">
            <button type="submit">Destroy Session</button>
        </form>
    </body>
</html>
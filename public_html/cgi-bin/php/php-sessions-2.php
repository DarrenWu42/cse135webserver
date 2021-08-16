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
        <h1 align=center>PHP Sessions Page 2</h1>
        <hr/>
        <p>
            <b>Name:</b> <?php echo(empty($_SESSION['name']) ? "None" : $_SESSION['name'])?><br/>
            <a href="/cgi-bin/php/php-sessions-1.php">Session Page 1</a><br/>
            <a href="/hw2/php-cgiform.html">PHP CGI Form</a>
        </p>
        <form style="margin-top:30px" action="/cgi-bin/php/php-destroy-session.php" method="get">
            <button type="submit">Destroy Session</button>
        </form>
    </body>
</html>
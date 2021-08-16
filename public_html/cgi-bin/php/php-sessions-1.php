<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
    session_start();
    parse_str(file_get_contents('php://input'), $_POST);
    if(!array_key_exists('name', $_SESSION)){
        $_SESSION['name'] = $_POST['username'];
    }
?>
<html>
    <head>
        <title>PHP Sessions</title>
    </head>
    <body>
        <h1>PHP Sessions Page 1</h1>
        <p>
            <b>Name:</b> <?php echo(array_key_exists('name', $_SESSION) ? $_SESSION['name'] : "None")?>

            <a href="/cgi-bin/php/php-sessions-2.php">Session Page 2</a>
            <a href="/cgi-bin/php/php-cgiform.html">PHP CGI Form</a>
        </p>
        <form style="margin-top:30px" action="/cgi-bin/php/php-destroy-session.php" method="get">
            <button type="submit">Destroy Session</button>
        </form>
    </body>
</html>
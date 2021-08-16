<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
?>
<html>
    <head>
        <title>General Request Echo</title>
    </head>
    <body>
        <h1 align="center">General Request Echo</h1>
        <hr/>
        <b>Protocol:</b>
        <?php
            echo "$_SERVER['SERVER_PROTOCOL']<br/>\n";
        ?>
        <b>Method:</b>
        <?php
            echo "$_SERVER['REQUEST_METHOD']<br/>\n";
        ?>
        <b>Query String:</b>
        <?php
            foreach ($_GET as $k => $v) {
                echo "<b>$k</b>: $v<br/>";
            }
        ?>
        <b>Message Body:</b>
        <?php
            parse_str(file_get_contents('php://input'), $_POST);
            foreach ($_POST as $k => $v) {
                echo "<b>$k</b>: $v<br/>";
            }
        ?>
    </body>
</html>
<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
?>
<html>
    <head>
        <title>POST Request Echo</title>
    </head>
    <body>
        <h1 align="center">POST Request Echo</h1>
        <hr/>
        <b>Message Body:</b><br/>
        <pre>
        <?php
            parse_str(file_get_contents('php://input'), $_POST);
            foreach ($_POST as $k => $v) {
                echo "<b>$k</b>: $v<br/>\n";
            }
        ?>
        </pre>
    </body>
</html>
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
        <?php
            foreach ($_POST as $k => $v) {
                echo "<b>$_POST[$k]</b>: $v<br/>\n";
            }
        ?>
    </body>
</html>
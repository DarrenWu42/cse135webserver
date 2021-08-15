<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
?>
<html>
    <head>
        <title>Environment Variables</title>
    </head>
    <body>
        <h1 align="center">Environment Variables</h1>
        <hr/>
        <?php
            foreach ($_SERVER as $k => $v) {
                echo "<b>$_SERVER[$k]<\b>: $v<br/>\n";
            }
        ?>
    </body>
</html>
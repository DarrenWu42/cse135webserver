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
        <?php
            foreach ($_SYSTEM as $k => $v) {
                echo "<b>$_SYSTEM[$k]<\b>: $v<br/>\n";
            }
        ?>
    </body>
</html>
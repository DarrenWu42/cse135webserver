<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
?>
<html>
    <head>
        <title>GET Request Echo</title>
    </head>
    <body>
        <h1 align="center">GET Request Echo</h1>
        <hr/>
        Query string: <br/>
        <pre>
        <?php
            echo var_dump($_POST);
        ?>
        <?php
            foreach ($_GET as $k => $v) {
                echo "<b>$_GET[$k]</b>: $v<br/>\n";
            }
        ?>
        </pre>
    </body>
</html>
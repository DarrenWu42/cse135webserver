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
            echo var_dump($_POST);
        ?>
        </pre>
    </body>
</html>
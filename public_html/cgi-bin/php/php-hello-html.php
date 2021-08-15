<?php
    header("Cache-Control: no-cache");
    header("Content-type: text/html");
?>
<html>
    <head>
        <title>Hello, PHP!</title>
    </head>
    <body>
        <h1 align=center>Hello, PHP!</h1>
        <hr/>
        Hello, World!<br/>
        <?php
            echo "This program was generated at: " . date("D M j H:i:s Y") . "<br/>\n";
            echo "Your current IP address is: " . $_SERVER['REMOTE_ADDR'] . "<br/>\n";
        ?>
    </body>
</html>
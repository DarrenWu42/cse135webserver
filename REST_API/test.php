<?php
$toHash = "4pache25";
$hashed = password_hash($toHash, PASSWORD_BCRYPT);
echo $hashed, "\n";
?>
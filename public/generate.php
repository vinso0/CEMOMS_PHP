<?php
$password = "admin"; // your chosen password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
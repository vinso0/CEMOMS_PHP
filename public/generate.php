<?php
$password = "Admin123!"; // your chosen password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
<?php
$host = 'localhost';
$db_username = 'main';
$db_password = 'main';
$db_name = 'spaces_db';

$conn = new mysqli($host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

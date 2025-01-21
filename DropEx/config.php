<?php
$conn = new mysqli('localhost', 'root', '', 'DropEx');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
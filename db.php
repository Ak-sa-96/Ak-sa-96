<?php
$conn = new mysqli("localhost", "root", "", "wpoets");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
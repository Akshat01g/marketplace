<?php
$conn = new mysqli("localhost", "root", "", "marketplace_db");
if ($conn->connect_error) { die("DB Error: " . $conn->connect_error); }
?>
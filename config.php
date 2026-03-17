<?php
session_start();

$conn = new mysqli("localhost", "root", "", "marketplace_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

if (!isset($_SESSION['lang'])) { 
    $_SESSION['lang'] = 'en'; 
}

$lang_data = [
    'en' => [
        'title' => "Local Marketplace", 
        'buy' => "Buy Now", 
        'sold' => "Sold Out",
        'price' => "Price", 
        'switch' => "हिंदी", 
        'order_msg' => "Your Orders"
    ],
    'hi' => [
        'title' => "स्थानीय बाज़ार", 
        'buy' => "अभी खरीदें", 
        'sold' => "बिक गया",
        'price' => "कीमत", 
        'switch' => "English", 
        'order_msg' => "आपके आदेश"
    ]
];

$text = $lang_data[$_SESSION['lang']];

$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['user_id']) && $current_page != 'index.php' && $current_page != 'register.php') {
    header("Location: index.php");
    exit();
}
?>
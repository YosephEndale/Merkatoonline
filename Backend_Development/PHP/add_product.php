<?php
session_start();
include_once "Backend_Development/Database/connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Handle product addition submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve product details from form fields
    // Adjust the code to fetch all necessary product details from the form
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock_quantity = $_POST['stock_quantity'];
    $product_code = $_POST['product_code'];
    $brand = $_POST['brand'];
    $shipping_info = $_POST['shipping_info'];

    // Insert new product into seller_products table
    $query = "INSERT INTO seller_products (seller_id, product_name, description, price, category_id, stock_quantity, product_code, brand, shipping_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssiiiss", $_SESSION['user_id'], $product_name, $description, $price, $category_id, $stock_quantity, $product_code, $brand, $shipping_info);
    $stmt->execute();

    // Redirect to profile page after product addition
    header("Location: profile.php");
    exit;
}
?>


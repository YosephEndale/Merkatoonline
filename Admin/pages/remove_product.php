<?php
// Include database connection
include_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php";

// Check if product ID is provided in the POST request
if (isset($_POST['product_id'])) {
    // Get the product ID from the POST data
    $product_id = $_POST['product_id'];

    // Remove the product from the database
    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        // Product removed successfully
        echo "Product removed successfully";
    } else {
        // Failed to remove product
        echo "Failed to remove product";
    }
} else {
    // Product ID is missing in the POST request
    echo "Product ID is missing";
}
?>

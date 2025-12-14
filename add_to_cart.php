<?php
session_start();

// Include your database connection file
include_once "Backend_Development/Database/connection.php";

if (isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details from the database
    $query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Calculate total price
        $total_price = $product['price'] * $quantity;

        // Add item to cart session
        if (isset($_SESSION['cart'])) {
            $_SESSION['cart'][] = array(
                'product_id' => $product_id,
                'product_name' => $product['product_name'],
                'quantity' => $quantity,
                'price' => $product['price'],
                'total_price' => $total_price
            );
        } else {
            $_SESSION['cart'] = array(
                array(
                    'product_id' => $product_id,
                    'product_name' => $product['product_name'],
                    'quantity' => $quantity,
                    'price' => $product['price'],
                    'total_price' => $total_price
                )
            );
        }

        // Redirect to cart page
        header("Location: cart.php");
        exit();
    } else {
        // Product not found
        echo "Product not found.";
    }
} else {
    // Invalid request
    echo "Invalid request.";
}
?>

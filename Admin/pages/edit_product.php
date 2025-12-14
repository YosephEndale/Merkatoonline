<?php
// Include database connection
include_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php";

// Check if product ID is provided in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    // Redirect to profile page if product ID is missing
    header("Location: profile.php");
    exit();
}

// Get the product ID from the URL parameter
$product_id = $_GET['product_id'];

// Retrieve product information from the database based on the product ID
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Display a form with the current product information pre-filled
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Add your CSS styles here -->
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form action="update_product.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" class="form-control">
            </div>
            <!-- Add other input fields for product information -->
            <button type="submit" class="btn btn-primary" name="update_product">Update Product</button>
        </form>
    </div>
</body>
</html>

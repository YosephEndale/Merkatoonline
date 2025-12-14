<?php
session_start();

// Include database connection
include_once "Backend_Development/Database/connection.php";

/// Check if product ID is provided in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    // Redirect to profile page if product ID is missing
    header("Location: profile.php");
    exit();
}

// Get the product ID from the URL parameter
$product_id = $_GET['product_id'];

// Handle the form submission to upload images
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_images"])) {
    $targetDir = "uploads/"; // Directory where images will be uploaded
    $allowTypes = array('jpg', 'jpeg', 'png'); // Allowed image file types

    // Loop through each uploaded file
    foreach ($_FILES['images']['name'] as $key => $image) {
        $targetFilePath = $targetDir . basename($image); // Path of the uploaded file on the server
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); // Get the file type

        // Check if file upload was successful
        if ($_FILES['images']['error'][$key] == UPLOAD_ERR_OK) {
            // Check if the file type is allowed
            if (in_array($fileType, $allowTypes)) {
                // Upload the file to the server
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFilePath)) {
                    // Insert the file details into the database
                    $query = "INSERT INTO images (product_id, image_path) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("is", $product_id, $targetFilePath);
                    $stmt->execute();
                } else {
                    echo "Failed to move uploaded file.";
                }
            } else {
                echo "File type not allowed.";
            }
        } else {
            echo "File upload error.";
        }
    }

    // Redirect back to profile page after image upload
    header("Location: profile.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Images</title>
    <!-- Add your CSS styles here -->
</head>
<body>
<div class="container">
    <h1>Upload Images</h1>
    <form action="add_images.php" method="post" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple required>
    <input type="submit" name="upload_images" value="Upload Images">
</form>
</div>
</body>
</html>

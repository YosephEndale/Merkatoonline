<?php
session_start();

// Include database connection
include_once "Backend_Development/Database/connection.php";

// Retrieve categories from the database
$query_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($conn, $query_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

// Initialize variables
$username = $email = $phone_number = $created_at = '';
$is_seller = false;

// Fetch user information from the database using the user_id from the session
$query = "SELECT username, email, phone_number, created_at, is_seller FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if user data is fetched successfully
if ($result->num_rows > 0) {
    $userInfo = $result->fetch_assoc();
    $username = $userInfo['username'];
    $email = $userInfo['email'];
    $phone_number = $userInfo['phone_number'];
    $created_at = $userInfo['created_at'];
    $is_seller = $userInfo['is_seller'];
}

// Handle the form submission to update the user's seller status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["become_seller"])) {
    // Toggle the is_seller status
    $is_seller = !$is_seller;

    // Update the user's is_seller status in the database
    $query = "UPDATE users SET is_seller = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $is_seller, $_SESSION['user_id']);
    $stmt->execute();
    // Redirect to the profile page to reflect the changes
    header("Location: profile.php");
    exit();
}

// Handle the form submission to add a new product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    // Retrieve form data
    $product_name = $_POST["product_name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category_id = $_POST["category_id"];
    $stock_quantity = $_POST["stock_quantity"];
    $product_code = $_POST["product_code"];
    $brand = $_POST["brand"];
    $shipping_info = $_POST["shipping_info"];
    $rating = $_POST["rating"];
    $material_feature = $_POST["material_feature"];
    $item_form = $_POST["item_form"];
    $scent = $_POST["scent"];

    // Insert the new product into the database
    $sql = "INSERT INTO products (product_name, description, price, category_id, stock_quantity, product_code, brand, shipping_info, rating, material_feature, item_form, scent, seller_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiiisdsissi", $product_name, $description, $price, $category_id, $stock_quantity, $product_code, $brand, $shipping_info, $rating, $material_feature, $item_form, $scent, $_SESSION['user_id']);
    $stmt->execute();
    $product_id = $stmt->insert_id;

    // Handle image uploads
    if (!empty($_FILES["images"]["name"][0])) {
        $upload_dir = "uploads/"; // Directory to store images
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB max size

        foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {
            $file_name = $_FILES["images"]["name"][$key];
            $file_size = $_FILES["images"]["size"][$key];
            $file_type = $_FILES["images"]["type"][$key];
            $file_error = $_FILES["images"]["error"][$key];

            // Validate file
            if ($file_error === UPLOAD_ERR_OK) {
                if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                    // Generate unique file name to avoid conflicts
                    $unique_name = uniqid() . "_" . basename($file_name);
                    $file_path = $upload_dir . $unique_name;

                    // Move the uploaded file to the uploads directory
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        // Insert image path into the images table
                        $sql = "INSERT INTO images (product_id, image_path) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("is", $product_id, $file_path);
                        $stmt->execute();
                    } else {
                        echo "Error moving file: $file_name";
                    }
                } else {
                    echo "Invalid file type or size for: $file_name";
                }
            } else {
                echo "Error uploading file: $file_name";
            }
        }
    }

    // Redirect to profile page after successful product and image upload
    header("Location: profile.php?message=Product added successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <!-- Swiper JavaScript -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="Frontend_development/CSS_styles/profile.css">
    <link rel="stylesheet" href="Frontend_development/CSS_styles/Home_page.css">
    <link rel="icon" type="image/png" href="http://merkatoonline/Screenshot 2024-01-02 224010.png">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-logo">
                <a href="index.php">
                    <img src="http://merkatoonline/Screenshot 2024-01-02 224010.png" alt="Your Logo Alt Text">
                </a>
            </div>
            <form action="Search.php" method="POST">
                <div class="nav-search form-control rounded-0">
                    <div class="search-container">
                        <select class="select-search" name="category">
                            <option value="All">All</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_name']; ?>"><?php echo $category['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" placeholder="Search..." class="search-input" name="search">
                        <button class="submit" type="submit" name="submit">Search</button>
                    </div>
                </div>
            </form>
            <div class="language">
                <select>
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="it">Italian</option>
                </select>
            </div>
            <div class="account">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $query = "SELECT username FROM users WHERE user_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $username = $row['username'];
                    }
                    ?>
                    <span id="logoutButtons">
                        <a href="profile.php" id="userName"><?php echo $username; ?></a>
                        <a href="Backend_Development/PHP/logout.php">Log Out</a>
                    </span>
                <?php else: ?>
                    <span id="loginButtons">
                        <a href="login.php">Log In</a>
                        <span>or</span>
                        <a href="register.html">Sign Up</a>
                    </span>
                <?php endif; ?>
            </div>
            <div class="orders">
                <a href="my_orders.php">Orders</a>
            </div>
            <div class="cart">
                <a href="cart.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </a>
            </div>
            <div class="messages">
                <a href="messages.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                        <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                    </svg>
                </a>
            </div>
        </nav>
    </header>
    <!-- Toggle Button for Sidebar -->
    <div class="toggle-btn">☰</div>
    <!-- Sidebar -->
    <aside class="sidenav">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav flex-column">
                <!-- Shop by Category -->
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">category</i>
                        </div>
                        <span class="nav-link-text ms-1">Shop by Category</span>
                    </a>
                    <ul class="sublist">
                        <li><a href="#">Beauty and Personal Care</a></li>
                        <li><a href="#">Clothing</a></li>
                        <li><a href="#">Electronics</a></li>
                        <li><a href="#">Home and Kitchen</a></li>
                    </ul>
                </li>
                <!-- Special Collections -->
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">collections</i>
                        </div>
                        <span class="nav-link-text ms-1">Special Collections</span>
                    </a>
                    <ul class="sublist">
                        <li><a href="#">Best Sellers</a></li>
                        <li><a href="#">New Arrivals</a></li>
                        <li><a href="#">Clearance Items</a></li>
                    </ul>
                </li>
                <!-- Services and Features -->
                <li class="nav-item">
                    <a class="nav-link text-white sidebar-link" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">build</i>
                        </div>
                        <span class="nav-link-text ms-1">Services and Features</span>
                    </a>
                    <ul class="sublist">
                        <li><a href="#">Gift Cards</a></li>
                        <li><a href="#">Product Reviews</a></li>
                        <li><a href="#">Customer Support</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="main-content border-radius-lg">
        <div class="container">
            <h1 class="profile-title">Welcome, <?php echo htmlspecialchars($username); ?></h1>
            <div class="profile-info">
                <h2>Profile Information</h2>
                <ul>
                    <li><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></li>
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></li>
                    <li><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></li>
                    <li><strong>Account Created At:</strong> <?php echo htmlspecialchars($created_at); ?></li>
                </ul>
            </div>

            <!-- Form to become a seller -->
            <?php if (!$is_seller): ?>
                <form class="become-seller-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <button type="submit" name="become_seller">Become a Seller</button>
                </form>
            <?php endif; ?>

            <!-- Form to add a new product -->
            <?php if ($is_seller): ?>
                <div class="add-product-form">
                    <h2>Add Product</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <label for="product_name">Product Name:</label>
                        <input type="text" id="product_name" name="product_name" required><br><br>

                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required></textarea><br><br>

                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" step="0.01" required><br><br>

                        <label for="product_code">Product Code:</label>
                        <input type="text" id="product_code" name="product_code" required><br><br>

                        <label for="brand">Brand:</label>
                        <input type="text" id="brand" name="brand" required><br><br>

                        <label for="shipping_info">Shipping Info:</label>
                        <input type="text" id="shipping_info" name="shipping_info" required><br><br>

                        <label for="rating">Rating:</label>
                        <input type="number" id="rating" name="rating" step="0.01" required><br><br>

                        <label for="material_feature">Material Feature:</label>
                        <input type="text" id="material_feature" name="material_feature" required><br><br>

                        <label for="item_form">Item Form:</label>
                        <input type="text" id="item_form" name="item_form" required><br><br>

                        <label for="scent">Scent:</label>
                        <input type="text" id="scent" name="scent" required><br><br>

                        <label for="category_id">Category:</label>
                        <select id="category_id" name="category_id" required>
                            <?php
                            $query = "SELECT c1.category_id, c1.category_name, c2.category_name AS parent_category_name 
                                      FROM categories c1
                                      LEFT JOIN categories c2 ON c1.parent_category_id = c2.category_id";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                $category_name = ($row['parent_category_name']) ? $row['parent_category_name'] . ' - ' . $row['category_name'] : $row['category_name'];
                                echo "<option value='" . $row['category_id'] . "'>" . $category_name . "</option>";
                            }
                            ?>
                        </select><br><br>

                        <label for="stock_quantity">Stock Quantity:</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" required><br><br>

                        <label for="images">Product Images:</label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" required><br><br>

                        <input type="submit" name="add_product" value="Add Product">
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <!-- Basic Footer -->
        <footer>
            <a href="#" class="footer-title">Back to top</a>
            <div class="footer-items">
                <ul>
                    <h3>Get to Know Us</h3>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Press Release</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
                <ul>
                    <h3>Connect with Us</h3>
                    <li><a href="https://www.linkedin.com/in/yoseph-endale-66b148291/">LinkedIn</a></li>
                    <li><a href="https://www.instagram.com/joss_y10/">Instagram</a></li>
                    <li><a href="http://Wa.me/+393277134214">WhatsApp</a></li>
                </ul>
                <ul>
                    <h3>Let Us Help You</h3>
                    <li><a href="#">Your Account</a></li>
                    <li><a href="#">Help</a></li>
                    <li><p>© 2024 Merkatoonline.com. All rights reserved.</p></li>
                </ul>
            </div>
        </footer>
    </main>
    <!-- Core JS Files -->
    <script src="Frontend_development/Javascript/account.js"></script>
    <script src="Frontend_development/Javascript/slideshow.js"></script>
    <script src="Frontend_development/Javascript/sidebar.js"></script>
</body>
</html>
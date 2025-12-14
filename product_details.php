
<?php
session_start();

include_once "Backend_Development/Database/connection.php";
// Retrieve categories from the database
$query_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($conn, $query_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

// Process form submission
if(isset($_POST['submit'])) {
    $search_query = $_POST['search'];
    $category_filter = $_POST['category'];

    // Build the query based on category filter
    $category_condition = "";
    if ($category_filter != "All") {
        $category_condition = "AND categories.category_name = '$category_filter'";
    }

    $query = "SELECT Products.*, images.image_path AS image_path 
    FROM Products 
    LEFT JOIN (
        SELECT product_id, image_path 
        FROM Images 
        WHERE image_path LIKE '%Product_card_pic.jpg'
    ) AS images ON Products.product_id = images.product_id
    LEFT JOIN categories ON Products.category_id = categories.category_id
    WHERE (Products.product_name LIKE '%$search_query%' OR Products.description LIKE '%$search_query%') $category_condition";
    
    $result = mysqli_query($conn, $query);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if(isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Retrieve product details
    $query = "SELECT Products.*, categories.category_name, users.username AS seller_name, users.user_id AS seller_id 
              FROM Products 
              LEFT JOIN categories ON Products.category_id = categories.category_id
              LEFT JOIN users ON Products.seller_id = users.user_id
              WHERE Products.product_id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
    
    if($product) {
        // Retrieve product images
        $query_images = "SELECT * FROM images WHERE product_id = $product_id";
        $result_images = mysqli_query($conn, $query_images);
        $images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

        // Check if the seller is an admin
        $seller_id = $product['seller_id'];
        $query_admin = "SELECT * FROM admin_users WHERE admin_id = $seller_id";
        $result_admin = mysqli_query($conn, $query_admin);
        $is_admin = mysqli_num_rows($result_admin) > 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <!-- Swiper JavaScript -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="Frontend_development/CSS_styles/product_details.css">
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
                <a href="cart.php" data-tooltip="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </a>
            </div>
            <div class="messages">
                <a href="messages.php" data-tooltip="Messages">
                    <i class="fas fa-comments"></i>
                </a>
            </div>
        </nav>
    </header>
    <main class="main-content border-radius-lg">
        <div class="product-details-container">
            <section class="product-images">
                <!-- Main Product Image -->
                <img id="main-image" src="<?php echo $images[0]['image_path']; ?>" alt="Product Image">
                <!-- Thumbnail Images -->
                <div class="thumbnail-images">
                    <?php foreach ($images as $index => $image): ?>
                        <img class="thumbnail" src="<?php echo $image['image_path']; ?>" alt="Product Thumbnail" data-index="<?php echo $index; ?>">
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="product-info">
                <!-- Product Name -->
                <h1><?php echo $product['product_name']; ?></h1>
                <!-- Product Description -->
                <div class="product-description">
                    <h2>Description</h2>
                    <p><?php echo $product['description']; ?></p>
                </div>
                <!-- Product Details -->
                <div class="product-details">
                    <h2>Product Details</h2>
                    <ul>
                        <li><strong>Category:</strong> <?php echo $product['category_name']; ?></li>
                        <li><strong>Price:</strong> €<?php echo $product['price']; ?></li>
                        <li><strong>Stock Quantity:</strong> €<?php echo $product['stock_quantity']; ?></li>
                        <li><strong>Brand:</strong> <?php echo $product['brand']; ?></li>
                        <li><strong>Material Feature:</strong> <?php echo $product['material_feature']; ?></li>
                        <li><strong>Item Form:</strong> <?php echo $product['item_form']; ?></li>
                        <li><strong>Scent:</strong> <?php echo $product['scent']; ?></li>
                    </ul>
                </div>
                <!-- Seller Information -->
                <?php if ($product['seller_id']): ?>
                    <div class="seller-info">
                        <h2>Seller Information</h2>
                        <p>Sold by: <?php echo $product['seller_name']; ?></p>
                        <?php if (!$is_admin && isset($_SESSION['user_id'])): ?>
                            <a href="messages.php?seller_id=<?php echo $product['seller_id']; ?>&product_id=<?php echo $product['product_id']; ?>" class="chat-with-seller-btn">
                                <i class="fas fa-comment-alt"></i> Chat with Seller
                            </a>
                        <?php elseif (!$is_admin && !isset($_SESSION['user_id'])): ?>
                            <p>Please <a href="login.php">log in</a> to chat with the seller.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <!-- Add to Cart Form -->
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Add to Cart</button>
                </form>
            </section>
        </div>
        <!-- Best Sellers Section -->
        <section class="best-sellers">
            <h2>Best Sellers</h2>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <!-- Best Sellers Products will be dynamically added here -->
                    <?php
                    $query_best_sellers = "SELECT Products.*, images.image_path AS image_path 
                    FROM Products 
                    LEFT JOIN (
                        SELECT product_id, image_path 
                        FROM Images 
                        WHERE image_path LIKE '%Product_card_pic.jpg'
                    ) AS images ON Products.product_id = images.product_id
                    ORDER BY RAND()
                    LIMIT 12";
                    $result_best_sellers = mysqli_query($conn, $query_best_sellers);
                    while ($best_seller = mysqli_fetch_assoc($result_best_sellers)) {
                        echo '<div class="swiper-slide">';
                        echo '<div class="product-card">';
                        echo '<img src="' . $best_seller['image_path'] . '" alt="' . $best_seller['product_name'] . '">';
                        echo '<h3>' . $best_seller['product_name'] . '</h3>';
                        echo '<p class="price">€' . $best_seller['price'] . '</p>';
                        echo '<a href="product_details.php?product_id=' . $best_seller['product_id'] . '">Buy now</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>
        <!-- Basic Footer -->
        <footer>
            <a href="#" class="footer-title">
                Back to top
            </a>
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
    <script src="Frontend_development/Javascript/product_image.js"></script>
    <script src="Frontend_development/Javascript/account.js"></script> 
    <script src="Frontend_development/Javascript/slideshow.js"></script>
</body>
</html>
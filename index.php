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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Merkatoonline.com</title>
 
<!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

<!-- Swiper JavaScript -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="Frontend_development/CSS_styles/Home_page.css">
<link rel="icon" type="image/png" href="http://merkatoonline/Screenshot 2024-01-02 224010.png">

</head>
<body>
    <header>
        <!-- Navigation Bar -->
        <nav class="navbar">
            <!-- Logo -->
            <div class="nav-logo">
                <a href="#">
                    <img src="http://merkatoonline/Screenshot 2024-01-02 224010.png" alt="Your Logo Alt Text">
                </a>
            </div>
            <!-- Search Form -->
            <form action="Search.php" method="POST">
                <div class="nav-search form-control rounded-0">
                    <div class="search-container">
                        <select class="select-search" name="category">
                            <option value="All">All</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['category_name']; ?>"><?php echo $category['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" placeholder="Search..." class="search-input" name="search">
                        <button class="submit" type="submit" name="submit">Search</button>
                    </div>
                </div>
            </form>
            <!-- Language Selection -->
            <div class="language">
                <select>
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="it">Italian</option>
                </select>
            </div>
            <!-- User Account Links -->
            <div class="account">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php
                    include_once "Backend_Development/Database/connection.php";
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
            <!-- User Orders -->
            <div class="orders">
                <a href="my_orders.php">Orders</a>
            </div>
            <!-- Shopping Cart -->
            <div class="cart">
                <a href="cart.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </a>
            </div>
            <!-- Messages -->
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
        <!-- Sidenav Content -->
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
<main class="main-content border-radius-lg ">
    
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

<!-- Advertisement Section -->
<section class="advertisement">
    <?php
    $query_ad = "SELECT * FROM advertising WHERE id = 2 LIMIT 1";
    $result_ad = mysqli_query($conn, $query_ad);
    
if (mysqli_num_rows($result_ad) > 0) {
    $ad = mysqli_fetch_assoc($result_ad);
    echo '<a href="' . $ad['url'] . '">';
    echo '<img src="' . $ad['image_url'] . '" alt="Advertisement Banner" class="ad-img">';
    echo '</a>';
} else {
    echo '<p>No advertisement found</p>';
}
?>
</section>

<!-- Featured Products Section -->
<section class="featured-products">
<h2>Featured Products</h2>
<div class="product-list">
    <?php
    $query_featured = "SELECT Products.*, images.image_path AS image_path 
    FROM Products 
    LEFT JOIN (
        SELECT product_id, image_path 
        FROM Images 
        WHERE image_path LIKE '%Product_card_pic.jpg'
    ) AS images ON Products.product_id = images.product_id
                        ORDER BY RAND()
                        LIMIT 4";
    $result_featured = mysqli_query($conn, $query_featured);

    while ($product = mysqli_fetch_assoc($result_featured)) {
        echo '<div class="product-card">';
        echo '<img src="' . $product['image_path'] . '" alt="' . $product['product_name'] . '">';
        echo '<h3>' . $product['product_name'] . '</h3>';
        echo '<p class="price">€' . $product['price'] . '</p>';
        echo '<a href="product_details.php?product_id=' . $product['product_id'] . '">Buy now</a>';
        echo '</div>';
    }
    ?>
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
    <div class="footer-login-signup">
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php
            include_once "Backend_Development/Database/connection.php";
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
    <div class="footer-orders-cart">
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
    </div>
</footer>
</main>

<!-- Core JS Files -->
<script src="Frontend_development/Javascript/slideshow.js"></script>
<script src="Frontend_development/Javascript/sidebar.js"></script>
<script src="Admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="Admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="Admin/assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>
</html>
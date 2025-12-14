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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Search Results</title>
 <!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

<!-- Swiper JavaScript -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="Frontend_development/CSS_styles/search.css">
    <link rel="icon" type="image/png" href="http://merkatoonline/Screenshot 2024-01-02 224010.png">
</head>
<body>
<!-- Include the header -->
<header>
    <!-- Navigation Bar -->
    <nav class="navbar">
         <!-- Logo -->
         <div class="nav-logo">
    <a href="index.php">
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
                    
                    <button type="submit" name="submit">Search</button>
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
        // Include database connection
        include_once "Backend_Development/Database/connection.php";

        // Prepare SQL statement to fetch username based on user_id
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
    </nav>
</header>

   <!-- Toggle Button for Sidebar -->
   <div class="toggle-btn">&#9776;</div>

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
<h1>Search Results for "<?php echo isset($search_query) ? $search_query : ''; ?>"</h1>
        <div class="product-list">
            <?php if(isset($products) && count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <?php if(isset($product['image_path'])): ?>
                            <img src="<?php echo $product['image_path']; ?>" alt="<?php echo isset($product['product_name']) ? $product['product_name'] : 'Product Image'; ?>">
                        <?php else: ?>
                            <img src="http://merkatoonline/placeholder.jpg" alt="Placeholder Image">
                        <?php endif; ?>
                        <h2 class="product-name"><?php echo isset($product['product_name']) ? $product['product_name'] : 'Product Name'; ?></h2>
                        <p class="price">€<?php echo isset($product['price']) ? $product['price'] : 'Price'; ?></p>
                        <a href="product_details.php?product_id=<?php echo isset($product['product_id']) ? $product['product_id'] : '#'; ?>">Buy Now</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found matching the search criteria.</p>
            <?php endif; ?>
        </div>
  

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
                <li> <p>&copy; 2024 Merkatoonline.com. All rights reserved.</p></li>
            </ul>
        </div>
    </footer>
    </main>
   
    <!--   Core JS Files   -->
<script src="Frontend_development/Javascript/account.js"></script> 
<script src="Frontend_development/Javascript/slideshow.js"></script>
<script src="Frontend_development/Javascript/sidebar.js"></script>
</body>
</html>

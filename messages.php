<?php
session_start();
include_once "Backend_Development/Database/connection.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch conversations (distinct sender/receiver pairs)
$conversations_query = "
    SELECT DISTINCT u.user_id, u.username, m.product_id, p.product_name,
        (SELECT COUNT(*) FROM messages m2 WHERE m2.receiver_id = ? AND m2.sender_id = u.user_id AND m2.is_read = 0) AS unread_count
    FROM messages m
    JOIN users u ON (u.user_id = m.sender_id OR u.user_id = m.receiver_id)
    LEFT JOIN products p ON m.product_id = p.product_id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY m.sent_at DESC";
$stmt = $conn->prepare($conversations_query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$conversations_result = $stmt->get_result();
$conversations = [];
while ($row = $conversations_result->fetch_assoc()) {
    if ($row['user_id'] != $user_id) { // Exclude the current user
        $conversations[] = $row;
    }
}

// Check if we're starting a new conversation from product_details.php
$selected_user_id = isset($_GET['seller_id']) ? (int)$_GET['seller_id'] : (isset($_GET['user_id']) ? (int)$_GET['user_id'] : null);
$selected_product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

// Fetch messages for the selected conversation (if any)
$messages = [];
if ($selected_user_id) {
    $messages_query = "
        SELECT m.message_id, m.sender_id, m.receiver_id, m.message_text, m.sent_at, m.is_read, u.username AS sender_username
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE (
            (m.sender_id = ? AND m.receiver_id = ?) OR
            (m.sender_id = ? AND m.receiver_id = ?)
        ) AND (m.product_id = ? OR m.product_id IS NULL)
        ORDER BY m.sent_at ASC";
    $stmt = $conn->prepare($messages_query);
    $stmt->bind_param("iiiii", $user_id, $selected_user_id, $selected_user_id, $user_id, $selected_product_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Mark messages as read
    $update_query = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $user_id, $selected_user_id);
    $stmt->execute();
} elseif (isset($_GET['seller_id']) && isset($_GET['product_id'])) {
    // If no messages exist, ensure the conversation panel is pre-selected with the seller and product
    $selected_user_id = (int)$_GET['seller_id'];
    $selected_product_id = (int)$_GET['product_id'];
}

// Handle sending a new message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message_text = htmlspecialchars($_POST['message_text']); // Sanitize input
    $product_id = $_POST['product_id'] ?: null;

    if (!empty($message_text) && $receiver_id != $user_id) {
        $insert_query = "INSERT INTO messages (sender_id, receiver_id, product_id, message_text) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiis", $user_id, $receiver_id, $product_id, $message_text);
        $stmt->execute();

        // Redirect to the same conversation
        header("Location: messages.php?user_id=$receiver_id" . ($product_id ? "&product_id=$product_id" : ""));
        exit();
    }
}

// Fetch categories for navbar
$query_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($conn, $query_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Merkatoonline</title>
    <!-- Font Awesome Icons (kept in case needed elsewhere) -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons for Sidebar -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="Frontend_development/CSS_styles/Messages.css">
    <link rel="icon" type="image/png" href="http://merkatoonline/Screenshot 2024-01-02 224010.png">
</head>
<body>
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
                <div class="nav-search">
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
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
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
    <div class="toggle-btn" aria-label="Toggle sidebar">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <aside class="sidenav">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav flex-column">
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
    <main class="main-content border-radius-lg">
        <div class="container">
            <h1>Messages</h1>
            <div class="messages-container">
                <div class="conversations-list">
                    <h2>Conversations</h2>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="messages.php?user_id=<?php echo $conv['user_id']; ?><?php echo $conv['product_id'] ? '&product_id=' . $conv['product_id'] : ''; ?>" class="conversation-item">
                            <strong><?php echo htmlspecialchars($conv['username']); ?></strong>
                            <?php if ($conv['product_id']): ?>
                                <p>Product: <?php echo htmlspecialchars($conv['product_name']); ?></p>
                            <?php endif; ?>
                            <?php if ($conv['unread_count'] > 0): ?>
                                <span class="unread-count"><?php echo $conv['unread_count']; ?> unread</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($conversations) && $selected_user_id): ?>
                        <a href="messages.php?user_id=<?php echo $selected_user_id; ?>&product_id=<?php echo $selected_product_id; ?>" class="conversation-item">
                            <strong><?php
                                $query = "SELECT username FROM users WHERE user_id = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $selected_user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                echo htmlspecialchars($result->fetch_assoc()['username']);
                            ?></strong>
                            <p>Product: <?php
                                $query = "SELECT product_name FROM products WHERE product_id = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $selected_product_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                echo htmlspecialchars($result->fetch_assoc()['product_name']);
                            ?></p>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="messages-panel">
                    <?php if ($selected_user_id): ?>
                        <h2>Conversation with <?php
                            $query = "SELECT username FROM users WHERE user_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $selected_user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            echo htmlspecialchars($result->fetch_assoc()['username']);
                        ?></h2>
                        <?php if ($selected_product_id): ?>
                            <p>Regarding: <?php
                                $query = "SELECT product_name FROM products WHERE product_id = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $selected_product_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                echo htmlspecialchars($result->fetch_assoc()['product_name']);
                            ?></p>
                        <?php endif; ?>
                        <div class="messages">
                            <?php foreach ($messages as $msg): ?>
                                <div class="message <?php echo $msg['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
                                    <p><strong><?php echo htmlspecialchars($msg['sender_username']); ?>:</strong> <?php echo htmlspecialchars($msg['message_text']); ?></p>
                                    <small><?php echo $msg['sent_at']; ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form class="message-form" method="post">
                            <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $selected_product_id; ?>">
                            <textarea name="message_text" placeholder="Type your message..." required></textarea>
                            <button type="submit" name="send_message">Send</button>
                        </form>
                    <?php else: ?>
                        <p>Select a conversation to view messages.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
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
        <div class="footer-login-signup">
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
        <div class="footer-orders-cart">
            <div class="orders">
                <a href="my_orders.php">Orders</a>
            </div>
            <div class="cart">
                <a href="cart.php" data-tooltip="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </a>
            </div>
            <div class="messages">
                <a href="messages.php" data-tooltip="Messages">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                        <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                    </svg>
                </a>
            </div>
        </div>
    </footer>
    <script src="Frontend_development/Javascript/slideshow.js"></script>
    <script src="Frontend_development/Javascript/sidebar.js"></script>
</body>
</html>
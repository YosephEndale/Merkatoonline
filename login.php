<?php
session_start();

// Include connection file
include 'Backend_Development/Database/connection.php';

$errors = []; // Initialize errors array

// Check if form data is submitted and if the REQUEST_METHOD key is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username_email"]) && isset($_POST["password"])) {
    // Retrieve form data
    $username_email = $_POST["username_email"];
    $password = $_POST["password"];

    // Prepare SQL statement to fetch user data based on username or email
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, verify password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password_hash"])) {
            // Password is correct, login successful
            $_SESSION["user_id"] = $row["user_id"];
            // Redirect to homepage or dashboard
            header("Location: http://merkatoonline/index.php");
            exit();
        } else {
            // Password is incorrect
            $errors[] = "Incorrect password. Please try again.";
        }
    } else {
        // User not found
        $errors[] = "User not found. Please register.";
    }

    $stmt->close();
}

// Include the HTML for the login form
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>User Login</title>
    <link rel="stylesheet" href="http://merkatoonline/Frontend_development/CSS_styles/login.css">
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
        </nav>
    </header>

    <h2>Login</h2>
    <?php if (!empty($errors)) { ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo $error; ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="username_email">Username or Email:</label>
        <input type="text" id="username_email" name="username_email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.html">Register</a></p>

    <footer>
        <!-- Footer content -->
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
                <li><a href="#">LinkedIn</a></li>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">WhatsApp</a></li>
            </ul>
            <ul>
                <h3>Let Us Help You</h3>
                <li><a href="#">Your Account</a></li>
                <li><a href="#">Help</a></li>
            </ul>
        </div>
    </footer>  
</body>
</html>

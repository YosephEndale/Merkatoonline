<?php
session_start();

// Include the database connection file
include '../Database/connection.php';

// Check if the user is logged in or has a user_id in the session
if (!isset($_SESSION['user_id'])) {
    // If no user_id is in the session, redirect to registration or login
    header("Location: register.php");
    exit;
}

// Check if the verification code form is submitted
$verification_error = null;
$verification_success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the verification code is provided
    if (isset($_POST["verification_code"])) {
        // Retrieve the verification code entered by the user
        $entered_code = trim($_POST["verification_code"]);

        // Query the database to retrieve the verification token for the specific user
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT verification_token FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($correct_code);
        $stmt->fetch();
        $stmt->close();

        if ($correct_code) {
            if ($entered_code === $correct_code) {
                // Verification successful, update is_verified status
                $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();

                // Clear the user_id from the session
                unset($_SESSION['user_id']);

                // Set success message and redirect to login
                $verification_success = "Email verification successful! Please log in.";
                header("Location: ../../login.php");
                exit;
            } else {
                // Verification failed, display an error message
                $verification_error = "Invalid verification code. Please try again.";
            }
        } else {
            $verification_error = "Error: User not found.";
        }
    } else {
        // Verification code not provided
        $verification_error = "Error: Verification code not provided.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Merkatoonline.com</title>
    <link rel="stylesheet" href="http://merkatoonline/Frontend_development/CSS_styles/Verify.css">
</head>
<body>
    <header>
        <!-- Navigation Bar -->
        <nav class="navbar">
            <!-- Logo -->
            <div class="nav-logo">
                <a href="index.php">
                    <img src="http://merkatoonline/Screenshot%202024-01-02%20224010.png" alt="Image">
                </a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Email Verification</h1>
        <?php if (isset($verification_error)): ?>
            <p style="color: red;"><?php echo $verification_error; ?></p>
        <?php elseif (isset($verification_success)): ?>
            <p style="color: green;"><?php echo $verification_success; ?></p>
        <?php endif; ?>
        <p>Please enter the verification code sent to your email address.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="verification_code">Verification Code:</label>
            <input type="text" id="verification_code" name="verification_code" required>
            <button type="submit">Verify</button>
        </form>
    </div>

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
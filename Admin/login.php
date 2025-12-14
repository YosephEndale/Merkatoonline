<?php
session_start();

// Include database connection file
include_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php";

// Check if the user is already logged in, redirect to admin if true
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: Admin/index.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username or email and password from the form
    $username_email = $_POST["username_email"];
    $password = $_POST["password"];

    // Prepare and execute SQL statement to fetch user details
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify if user exists and password is correct
    if ($user && $password === $user['password_hash']) {
        // Authentication successful, set session variables
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        // Redirect to admin page
        header("Location: index.php");
        exit;
    } else {
        // Invalid credentials
        $error = "Invalid username/email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username_email">Username or Email:</label><br>
        <input type="text" id="username_email" name="username_email"><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Login">
    </form>
    <?php
    if (isset($error)) {
        echo "<p>$error</p>";
    }
    ?>
</body>
</html>

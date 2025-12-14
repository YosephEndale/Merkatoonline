<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Start the session
session_start();

// Include PHPMailer
require_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\PHP\PHPMailer\PHPMailer.php";
require_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\PHP\PHPMailer\SMTP.php";
require_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\PHP\PHPMailer\Exception.php";

// Define the generateVerificationCode function
function generateVerificationCode()
{
    return rand(100000, 999999);
}

// Define the sendMail function
function sendMail($email, $verification_code)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings for SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $_ENV['SMTP_PORT'];

        // Set email sender and recipient
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($email);

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = 'Email verification from Merkatoonline';
        $mail->Body = "<p>Dear Customer,</p>
                     <p>Thank you for signing up with Merkatoonline! To complete your registration and access all the features of our e-commerce platform, please use the following verification code:</p>
                     <p><strong>$verification_code</strong></p>
                     <p>If you have not registered on Merkatoonline, please ignore this email.</p>
                     <p>Thank you,<br> The Merkatoonline Team</p>";

        // Attempt to send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

// Include the database connection file
include '../Database/connection.php';

// Retrieve form data
$username = isset($_POST["username"]) ? $_POST["username"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$phone_number = isset($_POST["phone_number"]) ? $_POST["phone_number"] : "";
$password_hash = isset($_POST["password_hash"]) ? password_hash($_POST["password_hash"], PASSWORD_DEFAULT) : "";
$confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";
$is_seller = isset($_POST["is_seller"]) ? 1 : 0;
$seller_name = isset($_POST["seller_name"]) ? $_POST["seller_name"] : "";
$business_info = isset($_POST["business_info"]) ? $_POST["business_info"] : "";

// Check if username is empty
if (empty($username)) {
    die("Username cannot be empty.");
}

// Check if password and confirm password match
if ($_POST["password_hash"] !== $_POST["confirm_password"]) {
    die("Passwords do not match.");
}

// Check if username already exists
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die("Username already exists. Please choose a different username.");
}

// Generate verification code
$verification_code = generateVerificationCode();

// Prepare SQL statement to insert data into the database
$sql = "INSERT INTO users (username, email, phone_number, password_hash, is_seller, seller_name, business_info, verification_token)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssisss", $username, $email, $phone_number, $password_hash, $is_seller, $seller_name, $business_info, $verification_code);

if ($stmt->execute()) {
    // Get the inserted user's ID
    $user_id = $stmt->insert_id;

    // Store the user_id in the session
    $_SESSION['user_id'] = $user_id;

    // Registration successful, send verification email
    if (sendMail($email, $verification_code)) {
        header("Location: verify.php");
        exit;
    } else {
        echo "Error sending verification email.";
    }
} else {
    echo "Error: " . $conn->error;
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
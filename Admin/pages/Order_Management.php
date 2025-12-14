<?php
session_start();
// Include database connection file
include_once "C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php";
// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" type="image/png" href="http://merkatoonline/Screenshot 2024-01-02 224010.png">
    <title>Merkatoonline.com</title>
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="http://merkatoonline/Admin/assets/css/material-dashboard.css" rel="stylesheet" />
    <!-- Font Awesome CDN link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Common styling */
        body {
            background-color: whiteSmoke;
        }

        .container {
            margin-top: 20px;
        }

        /* Order form styling */
        .admin-order-form-container {
            background-color: white;
            border: 1px solid lightGray;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .admin-order-form-container h3 {
            color: darkslategray;
            margin-bottom: 20px;
        }

        /* Order table styling */
        .custom-table {
            width: 100%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .custom-table th,
        .custom-table td {
            padding: 10px;
            text-align: center;
            overflow: hidden;
            max-height: 80px;
            /* Adjust the maximum height as needed */
            max-width: 200px;
            /* Adjust the maximum width as needed */
            white-space: nowrap;
            /* Prevent text wrapping */
            text-overflow: ellipsis;
            /* Truncate text with ellipsis */
        }

        .custom-table thead th {
            background-color: whiteSmoke;
            color: dimGray;
            border-bottom: 2px solid lightGray;
        }

        .custom-table tbody tr:nth-child(even) {
            background-color: whiteSmoke;
        }

        .custom-table tbody tr:hover {
            background-color: gainsboro;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main"
        style="background-color: darkslategray;">

        <!-- Sidenav Header -->
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="http://merkatoonline/Admin/index.html">
                <img src="http://merkatoonline/Screenshot 2024-01-02 224010.png" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold text-white">Admin Interface</span>
            </a>
        </div>

        <!-- Sidenav Content -->
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav flex-column">

                <!-- Profile Management -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="http://merkatoonline/Admin/pages/profile.php">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">account_circle</i>
                        </div>
                        <span class="nav-link-text ms-1">Profile Management</span>
                    </a>
                </li>
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="pages/dashboard.html">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                <!-- User Management -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">person</i>
                        </div>
                        <span class="nav-link-text ms-1">User Management</span>
                    </a>
                </li>

                <!-- Order Management -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">shopping_cart</i>
                        </div>
                        <span class="nav-link-text ms-1">Order Management</span>
                    </a>
                </li>

                <!-- Category Management -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">category</i>
                        </div>
                        <span class="nav-link-text ms-1">Category Management</span>
                    </a>
                </li>

                <!-- Product Management -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">inventory</i>
                        </div>
                        <span class="nav-link-text ms-1">Product Management</span>
                    </a>
                </li>

                <!-- Report Management -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">assessment</i>
                        </div>
                        <span class="nav-link-text ms-1">Report Management</span>
                    </a>
                </li>

                <!-- Billing -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">receipt_long</i>
                        </div>
                        <span class="nav-link-text ms-1">Billing</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">settings</i>
                        </div>
                        <span class="nav-link-text ms-1">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Order Management</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">Order Management</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">

                        <div class="input-group input-group-outline">
                            <label class="form-label">Type here...</label>
                            <input type="text" class="form-control">
                        </div>

                    </div>
                    <li class="nav-item d-flex align-items-center">
                        <a href="./pages/sign-in.html" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>

                            <span class="d-sm-inline d-none">Sign In</span>

                        </a>
                    </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Place New Order</h5>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Select Product:</label>
                            <select name="product_id" id="product_id" class="form-control">
                                <?php
                                // Include database connection
                                include 'C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php';
                                // Fetch products from database
                                $productQuery = "SELECT product_id, product_name FROM products";
                                $productResult = mysqli_query($conn, $productQuery);
                                while ($productRow = mysqli_fetch_assoc($productResult)) {
                                    echo "<option value='" . $productRow['product_id'] . "'>" . $productRow['product_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1">
                        </div>
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address:</label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3"></textarea>
                        </div>
                        <!-- Automatically populate the order date field with the current date and time -->
                        <input type="hidden" name="order_date" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <button type="submit" class="btn btn-primary" style="color: white; background-color: darkslategray">Place Order</button>
                    </form>
                </div>
            </div>
            <!-- Display Orders -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch recent orders from database and display them in the table
                                $orderQuery = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 10";
                                $orderResult = mysqli_query($conn, $orderQuery);
                                while ($orderRow = mysqli_fetch_assoc($orderResult)) {
                                    echo "<tr>";
                                    echo "<td>" . $orderRow['order_id'] . "</td>";
                                    echo "<td>" . $orderRow['user_id'] . "</td>";
                                    // Fetch product name based on product_id
                                    // You can modify this query according to your database structure
                                    $productId = $orderRow['product_id'];
                                    $productQuery = "SELECT product_name FROM products WHERE product_id = $productId";
                                    $productResult = mysqli_query($conn, $productQuery);
                                    $productName = ($productRow = mysqli_fetch_assoc($productResult)) ? $productRow['product_name'] : 'Unknown';
                                    echo "<td>" . $productName . "</td>";
                                    echo "<td>" . $orderRow['quantity'] . "</td>";
                                    echo "<td>" . $orderRow['order_date'] . "</td>";
                                    echo "<td>" . $orderRow['status'] . "</td>";
                                    // Add Edit and Delete buttons for each order
                                    echo "<td>";
                                    echo "<a href='#' class='btn btn-sm btn-primary'>Edit</a> ";
                                    echo "<a href='#' class='btn btn-sm btn-danger'>Delete</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer style="background-color: darkSlateGray; padding: 20px; color: white; border-radius: 10px;">
            <!-- Footer content -->
            <a href="#" class="footer-title" style="text-decoration: none; color: white; font-weight: bold; display: block; margin-bottom: 10px;">
                Back to top
            </a>
            <div class="footer-items" style="display: flex; justify-content: space-between;">
                <ul style="list-style: none; margin-right: 30px;">
                    <h3 style="margin-bottom: 10px; font-size: 18px; color:white;">Get to Know Us</h3>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">About us</a></li>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">Press Release</a></li>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">Contact Us</a></li>
                </ul>
                <ul style="list-style: none; margin-right: 30px;">
                    <h3 style="margin-bottom: 10px; font-size: 18px;color:white;">Connect with Us</h3>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">LinkedIn</a></li>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">Instagram</a></li>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">WhatsApp</a></li>
                </ul>
                <ul style="list-style: none;">
                    <h3 style="margin-bottom: 10px; font-size: 18px; color:white;">Let Us Help You</h3>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">Your Account</a></li>
                    <li><a href="#" style="text-decoration: none; color: white; display: block; margin-bottom: 5px;">Help</a></li>
                </ul>
            </div>
        </footer>
    </main>
    <!-- Core JS Files -->
    <script src="../assets/js/core/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/material-dashboard.min.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <!-- JavaScript code for edit and remove buttons -->
    <script>
        $(document).ready(function() {
            // Remove button click event handler
            $('.btn-remove').click(function() {
                var orderId = $(this).closest('tr').find('td:first').text();
                var confirmRemove = confirm('Are you sure you want to remove this order?');
                if (confirmRemove) {
                    // Send an AJAX request to remove the order from the database
                    $.ajax({
                        url: '',
                        method: 'POST',
                        data: {
                            remove_order: true,
                            order_id: orderId
                        },
                        success: function(response) {
                            alert(response); // Display the response message
                            location.reload(); // Reload the page after removing the order
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            // Edit button click event handler
            $('.btn-edit').click(function() {
                // Handle the edit functionality here
            });
        });
    </script>

</body>

</html>

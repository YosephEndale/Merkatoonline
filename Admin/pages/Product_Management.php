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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="http://merkatoonline/Admin/assets/css/material-dashboard.css" rel="stylesheet">
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Common styling */
        body {
            background-color: whiteSmoke;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            margin-top: 20px;
            padding: 0 20px;
        }

        /* Product form styling */
        .admin-product-form-container {
            background-color: white;
            border: 1px solid lightGray;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .admin-product-form-container h3 {
            color: darkslategray;
            margin-bottom: 20px;
        }

        /* Product table styling */
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
            max-height: 80px; /* Adjust the maximum height as needed */
            max-width: 200px; /* Adjust the maximum width as needed */
            white-space: nowrap; /* Prevent text wrapping */
            text-overflow: ellipsis; /* Truncate text with ellipsis */
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

        .btn-edit,
        .btn-remove {
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-edit:hover,
        .btn-remove:hover {
            background-color: #ccc;
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-200">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main" style="background-color: darkslategray;">

    <!-- Sidenav Header -->
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Product Management</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Product Management</h6>
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
    <div class="row">
      <div class="col-md-6">
        <div class="admin-product-form-container">
          <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <h3>Add a New Product</h3>
            <div class="form-group">
              <input type="text" placeholder="Enter Product ID" name="Product_id" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter product name" name="product_name" class="form-control">
            </div>
            <div class="form-group">
              <input type="number" placeholder="Enter product price" name="product_price" class="form-control">
            </div>
            <div class="form-group">
              <textarea placeholder="Enter product description" name="product_description" class="form-control"></textarea>
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter category ID" name="category_id" class="form-control">
            </div>
            <div class="form-group">
              <input type="number" min="0" placeholder="Enter stock quantity" name="stock_quantity" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter product code" name="product_code" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter brand" name="brand" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter Shipping Information" name="shipping_info" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter Parent Category ID" name="parent_category_id" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter Material Feaure" name="Material_Feature" class="form-control">
            </div>
            <div class="form-group">
              <input type="text" placeholder="Enter Item Form" name="Item_Form" class="form-control">
            </div>
            <div class="form-group">
              <label for="product_image">Upload Product Image:</label>
              <input type="file" id="product_image" name="product_image" class="form-control">
            </div>
            <input type="submit" class="btn btn-dark" name="add_product" value="Add Product">
          </form>
        </div>
      </div>
      <div class="row">
      <div class="col-md-12">
        <div class="table-responsive custom-table">
          <h3>Product List</h3>
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category ID</th>
                <th>Stock Quantity</th>
                <th>Product Code</th>
                <th>Brand</th>
                <th>Shipping Information</th>
                <th>Parent Category ID</th>
                <th>Material Feature</th>
                <th>Item Form</th>
                <th>Actions</th> 
              </tr>
            </thead>
                <tbody>
                  <?php
                  include 'C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php';
                  $select = mysqli_query($conn, "SELECT * FROM products");
                  while ($row = mysqli_fetch_assoc($select)) { ?>
                    <tr>
                    <td><?php echo $row['product_id']; ?></td>
                      <td><?php echo $row['product_name']; ?></td>
                      <td><?php echo $row['description']; ?></td>
                      <td>$<?php echo $row['price']; ?>/-</td>
                      <td><?php echo $row['category_id']; ?></td>
                      <td><?php echo $row['stock_quantity']; ?></td>
                      <td><?php echo $row['product_code']; ?></td>
                      <td><?php echo $row['brand']; ?></td>
                      <td><?php echo $row['shipping_info']; ?></td>
                      <td><?php echo $row['parent_category_id']; ?></td>
                      <td><?php echo $row['material_feature']; ?></td>
                      <td><?php echo $row['item_form']; ?></td>
                      <td>
                  <!-- Edit button -->
                  <button class="btn btn-primary btn-edit" onclick="handleEdit(<?php echo $row['product_id']; ?>)">Edit</button>
                  <!-- Remove button -->
                  <button class="btn btn-remove" onclick="handleDelete(<?php echo $row['product_id']; ?>)">Remove</button>
                </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
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
    </div>
  </main>
  <!--   Core JS Files   -->
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
        var productId = $(this).closest('tr').find('td:first').text();
        var confirmRemove = confirm('Are you sure you want to remove this product?');
        if (confirmRemove) {
            // Send an AJAX request to remove the product from the database
            $.post('remove_product.php', { product_id: productId }, function(response) {
                alert(response); // Display the response message
                location.reload(); // Reload the page after removing the product
            }).fail(function(xhr, status, error) {
                console.error(xhr.responseText);
            });
        }
    });

    // Edit button click event handler
    $('.btn-edit').click(function() {
        var productId = $(this).closest('tr').find('td:first').text();
        // Redirect to the edit script with the product ID as a query parameter
        window.location.href = 'edit_product.php?product_id=' + productId;
    });
});

</script>

</body>

</html>

<?php
include 'C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php';

if(isset($_POST['add_product'])){
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    // Make sure to adjust the names of the following fields based on your HTML form
    $category_id = $_POST['category_id'];
    $stock_quantity = $_POST['stock_quantity'];
    $product_code = $_POST['product_code'];
    $brand = $_POST['brand'];
    $shipping_info = $_POST['shipping_info'];
    $parent_category_id = $_POST['parent_category_id'];
    $material_feature = $_POST['material_feature'];
    $item_form = $_POST['item_form'];
   
    if(empty($product_id) || empty($product_name) || empty($price) || empty($description) || empty($category_id) || empty($stock_quantity) || empty($product_code) || empty($brand) || empty($shipping_info) || empty($parent_category_id) || empty($material_feature) || empty($item_form)){
        $message[] = 'Please fill out all fields!';
    } else {
        // Insert the new product into the database
        $insert = "INSERT INTO products(product_id, product_name, price, description, category_id, stock_quantity, product_code, brand, shipping_info, parent_category_id, material_feature, item_form) VALUES('$product_id', '$product_name', '$price', '$description', '$category_id', '$stock_quantity', '$product_code', '$brand', '$shipping_info', '$parent_category_id', '$material_feature', '$item_form')";
        $upload = mysqli_query($conn,$insert);
        if($upload){
            $message[] = 'New product added successfully';
        } else {
            $message[] = 'Could not add the product';
        }
    }
}
if(isset($_POST['remove_product'])){
  $product_id = $_POST['product_id'];
  
  // Make sure to sanitize the input to prevent SQL injection
  $product_id = mysqli_real_escape_string($conn, $product_id);

  // Perform the deletion of the product
  $delete_query = "DELETE FROM products WHERE product_id = '$product_id'";
  $result = mysqli_query($conn, $delete_query);

  if($result){
      echo "Product removed successfully!";
  } else {
      echo "Failed to remove product!";
  }
}
?>


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
  <title>
    Merkatoonline.com
  </title>
  <!--     Fonts and icons     -->
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
        body {
            background-color: whiteSmoke;
        }

        .container {
            margin-top: 20px;
        }

        .admin-form-container {
            background-color: white;
            border: 1px solid lightGray;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .admin-form-container h3 {
            color: darkslategray;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: darkslategray;
            border-color: darkslategray;
        }

        .btn-primary:hover {
            background-color: #304e56;
            border-color: #304e56;
        }

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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Category Management</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Category Management</h6>
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
  <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="admin-form-container">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <h3>Add a New Category</h3>
                        <div class="form-group">
                            <input type="text" placeholder="Enter Category Name" name="category_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Enter Parent Category ID" name="parent_category_id" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-responsive custom-table">
                    <h3>Category List</h3>
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Parent Category ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php';
                            $select = mysqli_query($conn, "SELECT * FROM categories");
                            while ($row = mysqli_fetch_assoc($select)) { ?>
                                <tr>
                                    <td><?php echo $row['category_id']; ?></td>
                                    <td><?php echo $row['category_name']; ?></td>
                                    <td><?php echo $row['parent_category_id']; ?></td>
                                    <td>
                                        <!-- Edit button -->
                                        <button class="btn btn-primary btn-edit">Edit</button>
                                        <!-- Remove button -->
                                        <button class="btn btn-danger btn-remove">Remove</button>
                                    </td>
                                </tr>
                            <?php } ?>
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
    </div>
  </main>
    <!-- Bootstrap JS -->
    <script src="../assets/js/core/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- JavaScript code for edit and remove buttons -->
    <script>
    $(document).ready(function() {
        // Add category form submission using AJAX
        $('form').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert(response); // Display success message
                    $('#category-list').load('<?php echo $_SERVER['PHP_SELF']; ?> #category-list'); // Reload category list
                    $('form')[0].reset(); // Reset form fields
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Remove button click event handler
        $('.btn-remove').click(function() {
            // Your existing code for removing a category
        });

        // Edit button click event handler
        $('.btn-edit').click(function() {
            // Your existing code for editing a category
        });
    });
</script>

    <?php
    include 'C:\Users\yosep\OneDrive\Desktop\Web Programming project\Backend_Development\Database\connection.php';

    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];
        $parent_category_id = $_POST['parent_category_id'];

        if (empty($category_name) || empty($parent_category_id)) {
            $message[] = 'Please fill out all fields!';
        } else {
            // Insert the new category into the database
            $insert = "INSERT INTO categories(category_name, parent_category_id) VALUES('$category_name', '$parent_category_id')";
            $upload = mysqli_query($conn, $insert);
            if ($upload) {
                $message[] = 'New category added successfully';
            } else {
                $message[] = 'Could not add the category';
            }
        }
    }
    ?>
</body>

</html>

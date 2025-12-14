<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <!-- CSS styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #666;
            margin-bottom: 10px;
        }

        .order-summary {
            margin-bottom: 20px;
        }

        .order-summary h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .order-summary table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .order-summary table th,
        .order-summary table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .order-summary table th {
            background-color: #f2f2f2;
        }

        .total {
            font-size: 18px;
            margin-top: 20px;
            color: #333;
        }

        .thank-you-message {
            margin-top: 20px;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thank You for Your Order!</h1>
        <div class="order-summary">
            <h2>Order Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- PHP code to fetch and display cart items -->
                    <?php
                    session_start();
                    include_once "Backend_Development/Database/connection.php";

                    if (!empty($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $product_id => $quantity) {
                            $query = "SELECT * FROM products WHERE product_id = $product_id";
                            $result = mysqli_query($conn, $query);
                            $product = mysqli_fetch_assoc($result);
                            ?>
                            <tr>
                                <td><?php echo $product['product_name']; ?></td>
                                <td>$<?php echo $product['price']; ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td>$<?php echo $product['price'] * $quantity; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
            <div class="total">
                <span>Total: $<?php echo calculate_total($conn); ?></span>
            </div>
        </div>
        <p class="thank-you-message">Your order has been placed successfully. You will receive a confirmation email shortly.</p>
    </div>
</body>
</html>

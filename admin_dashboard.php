<?php
// Start the session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once 'config.php';

// Retrieve orders from the orders table
$sql = "SELECT * FROM orders";
$result = $mysqli->query($sql);

// Retrieve food items from the food_items table
$sql_food_items = "SELECT * FROM food_items";
$result_food_items = $mysqli->query($sql_food_items);

// Retrieve users from the users table
$sql_users = "SELECT * FROM users";
$result_users = $mysqli->query($sql_users);

$sql_orders = "SELECT o.order_id, o.user_id, o.order_date, f.food_item_name, f.price
               FROM orders o
               JOIN food_items f ON o.food_item_id = f.food_item_id";



// Prepare the statement
$stmt_orders = $mysqli->prepare($sql_orders);

// Execute the statement
$stmt_orders->execute();

// Get the result
$result_orders = $stmt_orders->get_result();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            padding-top: 70px;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 999;
        }

        .form-container {
            margin: 0 auto;
            width: 500px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 15px;
        }
    </style>
</head>
<style>
    .food-item-image {
        max-width: 100px;
        max-height: 100px;
    }

   
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <a class="navbar-brand" href="admin_dashboard.php">
            <!-- <img src="photo-1546069901-ba9599a7e63c.jpeg" alt="Profile Icon" width="30" height="30"> -->

            <i class="fas fa-user person-icon"></i>
            <span class="text-white ml-2" id="username"></span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="food_items.php">Food Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="users.php">Users</a>
                </li>
                <li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_add_items.php">Add items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <center>
            <h2>Admin Dashboard</h2>
        </center>
        <h3>Orders</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Username</th>
                    <th>Food name</th>
                    <th>Price</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_orders->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo $row['order_id']; ?>
                        </td>
                        <td>
                            <?php echo $row['user_id']; ?>
                        </td>
                        <td><?php echo $row['food_item_name']; ?></td>
                        <td><?php echo 'Tsh. '.$row['price'].'/='; ?></td>
                        <td>
                            <?php echo $row['order_date']; ?>
                        </td>
                        <td>
                        <a href="acceptorder.php?order_id=<?php echo $row['order_id']?>" class="btn btn-success btn-sm">Accept</a>
                        <a href="deleteorder.php?order_id=<?php echo $row['order_id']?>" class="btn btn-danger btn-sm">Deny</a>                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Retrieve the username from the session data
            const username = '<?php echo $_SESSION["username"] ?? ""; ?>';
            document.getElementById('username').textContent = username;
        });
    </script>
</body>

</html>
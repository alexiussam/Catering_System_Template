<?php
error_reporting(0);
// Start the session
session_start();

// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once 'config.php';

// Get the user ID from the session
$user_id = $_SESSION['username'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected food item name from the form
    $food_item_name = $_POST['food_item_name'];


    // Prepare a SELECT statement to retrieve the food_item_id based on the food_item_name
    $stmt_food_item = $mysqli->prepare("SELECT food_item_id FROM food_items WHERE food_item_name = ?");
    $stmt_food_item->bind_param("s", $food_item_name);
    $stmt_food_item->execute();

    // Get the result
    $result_food_item = $stmt_food_item->get_result();

    if($result_food_item->num_rows < 0){
        echo "Food Items Not Found";
    }

    // Fetch the food_item_id
    $food_item = $result_food_item->fetch_assoc();
    $food_item_id = $food_item['food_item_id'];

    // Get the current date and time
    $order_date = date('Y-m-d H:i:s');
    $status = 'pending';
    // Prepare an INSERT statement to add the order to the orders table
    $sql = "INSERT INTO orders (user_id, food_item_id, order_date, status) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $user_id, $food_item_id, $order_date, $status);
    $stmt->execute();
    $stmt->close();
}

// Prepare a SELECT statement to retrieve the user's orders
$sql_orders = "SELECT o.order_id, o.order_date, o.status, f.food_item_name, f.price
               FROM orders o
               JOIN food_items f ON o.food_item_id = f.food_item_id
               WHERE o.user_id = ?";

// Prepare the statement
$stmt_orders = $mysqli->prepare($sql_orders);

// Bind the user ID parameter
$stmt_orders->bind_param("s", $user_id);

// Execute the statement
$stmt_orders->execute();

// Get the result
$result_orders = $stmt_orders->get_result();

// Close database connection
$mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-user person-icon"></i>
            <span class="text-white ml-2" id="username"></span>
        </a>
        <!-- Rest of the code -->
    </nav>
    <div class="container">
        <br><center><div><h3>Your Orders</h3></div></center>
        <table class="table">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Food Item</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_orders->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['order_date']; ?></td>
                        <td><?php echo $row['food_item_name']; ?></td>
                        <td><?php echo "Tsh. ". $row['price']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <a href="deleteFood.php?id=<?php echo $row['order_id'];?>" name="delete_order" class="btn btn-danger btn-sm">Delete</a>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <form method="POST">
            <div class="form-group">
                <label for="food_item_name">Food Item Name:</label>
                <input type="text" class="form-control" id="food_item_name" name="food_item_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
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

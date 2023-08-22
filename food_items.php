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

// Retrieve food items from the food_items table
$sql_food_items = "SELECT * FROM food_items";
$result_food_items = $mysqli->query($sql_food_items);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Food Items</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

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
   <center> <h3>Food Items</h3></center>
        <table class="table">
            <thead>
                <tr>
                    <th>Food Item Name</th>
                    <th>Price</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_food_items->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo $row['food_item_name']; ?>
                        </td>
                        <td>
                            <?php echo "Tsh. ". $row['price']. "/="; ?>
                        </td>
                        <td>
                            <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_url']) . '" class="food-item-image" style="border-radius: 50%" alt="' . $row['food_item_name'] . '">'; ?>
                        </td>
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

<?php
// Start the session
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Display username on the dashboard
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <a class="navbar-brand" href="dashboard.php">
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
                    <a class="nav-link text-white" href="place_order.php">Place Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="update_profile.php">Update Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
     <center>   <h1>Welcome to our catering system, feel free to place your order</h1> </center>

        <form class="form-inline mb-4" method="get" action="" id="searchForm">
            <div class="input-group" style="width: 100%;">
                <input type="text" class="form-control" name="search" placeholder="Search Food Items"
                    style="width: 70%;">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <span class="fa fa-search"></span>
                    </button>
                </div>

            </div>
        </form <div class="container">

        <center>
            <h2>Available Food Items</h2>
        </center>
        <div class="row" id="foodItems">
            <!-- Fetch and display food items from the database -->
            <?php

            require_once 'config.php';

            // Prepare and execute a query to fetch food items
            $sql = "SELECT * FROM food_items";
            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4">';
                    echo '<div class="card" style="background-color: wheat;">';
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_url']) . '" class="card-img-top" alt="' . $row['food_item_name'] . '" style="height: 250px; border-radius: 10px">';
                    echo '<div class="card-body">';
                    echo '<h4 class="card-title">' . $row['food_item_name'] . '</h4>';
                    echo '<p class="card-text">Price: Tsh ' . $row['price'] . '</p>';
                    echo '<a href="#" class="btn btn-primary">Available for order</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No food items found.</p>";
            }
            ?>
        </div>

    </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            searchFoodItems();
        });

        function searchFoodItems() {
            let searchQuery = document.querySelector('input[name="search"]').value.toLowerCase();
            let foodItems = document.getElementById('foodItems').children;

            for (let i = 0; i < foodItems.length; i++) {
                let foodItem = foodItems[i];
                let foodItemName = foodItem.querySelector('.card-title').textContent.toLowerCase();

                if (foodItemName.includes(searchQuery)) {
                    foodItem.style.display = 'block';
                } else {
                    foodItem.style.display = 'none';
                }
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Retrieve the username from the session data
            const username = '<?php echo $_SESSION["username"] ?? ""; ?>';
            document.getElementById('username').textContent = username;
        });
    </script>

</body>

</html>
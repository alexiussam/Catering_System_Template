<?php
// Start the session
session_start();

// Check if the admin is logged in, redirect to login page if not
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once 'config.php';

// Define variables and set to empty values
$foodItemName = $price = '';
$foodItemNameErr = $priceErr = $imageURLErr = '';

// Process add item form submission
if(isset($_POST["btn"])){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //
        function validateInputs($data){
            $data = trim($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        // Validate food item name
        if (empty($_POST['food_item_name'])) {
            $foodItemNameErr = 'Food item name is required';
        } else {
            $foodItemName = validateInputs($_POST['food_item_name']);
        }
    
      // Validate price
    if (empty($_POST['price'])) {
        $priceErr = 'Price is required';
    } elseif (!is_numeric($_POST['price'])) {
        $priceErr = 'Price must be a number';
    } elseif ($_POST['price'] < 5000 || $_POST['price'] > 200000) {
        $priceErr = 'Price must be between 5,000 to 200,000';
    } else {
        $price = validateInputs($_POST['price']);
    }
        // Validate image data
        if (empty($_FILES['image_url']['name'])) {
            $imageURLErr = 'Image is required';
        } else {
            // Get the temporary location of the uploaded file
            $imageTemp = $_FILES['image_url']['tmp_name'];
            $allowedExtensions = ['jpeg', 'jpg', 'png'];
            $fileExtension = strtolower(pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $imageURLErr = 'Invalid file extension. Only JPEG, JPG, and PNG files are allowed.';
            } else {
               
            // Read the image file
            $imageData = file_get_contents($imageTemp);
    
            if ($imageData === false) {
                $imageURLErr = 'Failed to read image file';
            } else {
                // If there are no errors, insert the item into the food_items table
                if (empty($foodItemNameErr) && empty($priceErr)) {
                    // Prepare an INSERT statement
                    $sql = "INSERT INTO food_items (food_item_name, price, image_url) VALUES (?, ?, ?)";
                    if ($stmt = $mysqli->prepare($sql)) {
                        // Bind variables to the prepared statement as parameters
                        $stmt->bind_param("sss", $foodItemName, $price, $imageData);
    
                        // Execute the prepared statement
                        
                        if ($stmt->execute()) {
                            // Update successful
                            $updateStatus = 'Successfully uploaded your details';
                        } else {
                            // Display the specific error message
                            echo 'Oops! Something went wrong: ' . $stmt->error;
                        }
                    } else {
                        echo 'Oops! Something went wrong. Please try again later.';
                    }
    
                    // Close statement
                    $stmt->close();
                }
            }
        }
    }
    }
}
// Close database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Items</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        .container {
            max-width: 500px;
        }
    </style>
</head>

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
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <center><h2>Add Item</h2></center>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Form fields -->
            <div class="form-group">
                <label for="food_item_name">Food Item Name:</label>
                <input type="text" name="food_item_name" id="food_item_name" class="form-control" value="<?php echo $foodItemName; ?>">
                <span class="text-danger"><?php echo $foodItemNameErr; ?></span>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" name="price" id="price" class="form-control" value="<?php echo $price; ?>">
                <span class="text-danger"><?php echo $priceErr; ?></span>
            </div>
            <div class="form-group">
                <label for="image_url">Image:</label>
                <input type="file" name="image_url" id="image_url" class="form-control-file">
                <span class="text-danger"><?php echo $imageURLErr; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" name="btn" value="Add Item" class="btn btn-primary">
            </div>
        </form>
        <?php if (!empty($updateStatus)) : ?>
            <div class="alert alert-success"><?php echo $updateStatus; ?></div>
        <?php endif; ?>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Retrieve the username from the session data
            const username = '<?php echo $_SESSION["username"] ?? ""; ?>';
            document.getElementById('username').textContent = username;
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>

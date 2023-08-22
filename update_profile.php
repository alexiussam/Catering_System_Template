<?php
// Start the session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}


// Include database connection
require_once 'config.php';

// Define variables and set to empty values
$fname = $lname = $username = $phone = '';
$fnameErr = $lnameErr = $usernameErr = $phoneErr = '';
$updateStatus = '';

// Fetch current user's data from the database
$currentUser = $_SESSION['username'];
$sql = "SELECT fname, lname, username, phone FROM users WHERE username = ?";
if ($stmt = $mysqli->prepare($sql)) {
    // Bind variable to the prepared statement as parameter
    $stmt->bind_param("s", $currentUser);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // Store the result
        $stmt->store_result();

        // Bind result variables
        $stmt->bind_result($fname, $lname, $username, $phone);

        // Fetch the result (there should be only one row)
        $stmt->fetch();
    } else {
        echo 'Oops! Something went wrong. Please try again later.';
    }

    // Close statement
    $stmt->close();
}

// Process profile update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty($_POST['fname'])) {
        $fnameErr = 'First name is required';
    } else {
        $fname = $_POST['fname'];
    }

    // Validate last name
    if (empty($_POST['lname'])) {
        $lnameErr = 'Last name is required';
    } else {
        $lname = $_POST['lname'];
    }

    // Validate username
    if (empty($_POST['username'])) {
        $usernameErr = 'Username is required';
    } else {
        $username = $_POST['username'];

        // Check if the new username already exists
        $sql = "SELECT id FROM users WHERE username = ? AND username <> ?";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $username, $currentUser);

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Store the result
                $stmt->store_result();

                // Check if username exists
                if ($stmt->num_rows == 1) {
                    $usernameErr = 'Username is already taken';
                }
            } else {
                echo 'Oops! Something went wrong. Please try again later.';
            }

            // Close statement
            $stmt->close();
        }
    }

    // Validate phone number
    if (empty($_POST['phone'])) {
        $phoneErr = 'Phone number is required';
    } else {
        $phone = $_POST['phone'];
    }

    // Check if there are no errors, update the user's profile
    if (empty($fnameErr) && empty($lnameErr) && empty($usernameErr) && empty($phoneErr)) {
        // Prepare an UPDATE statement
        $sql = "UPDATE users SET fname = ?, lname = ?, username = ?, phone = ? WHERE username = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssss", $fname, $lname, $username, $phone, $currentUser);

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Update successful
                $updateStatus = 'Successfully updated your details';
            } else {
                // Display the specific error message
                echo 'Oops! Something went wrong: ' . $stmt->error;
            }

            // Close statement
            $stmt->close();
        } 
    }


}


// Close database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update Profile</title>
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

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <a class="navbar-brand text-white"  href="dashboard.php"><i class="fas fa-home"></i>Home</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link text-white" href="logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <br><br><br><br><br>
    <div class="form-container">
        <h2>Update Profile</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Form fields -->
            <div class="form-group">
                <label>First name:</label>
                <input type="text" name="fname" class="form-control" value="<?php echo $fname; ?>">
                <span class="text-danger"><?php echo $fnameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Last name:</label>
                <input type="text" name="lname" class="form-control" value="<?php echo $lname; ?>">
                <span class="text-danger"><?php echo $lnameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="text-danger"><?php echo $usernameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Phone number:</label>
                <input type="tel" name="phone" class="form-control" value="<?php echo $phone; ?>">
                <span class="text-danger"><?php echo $phoneErr; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Update" class="btn btn-primary">
            </div>
        </form>
        
        <?php if (!empty($updateStatus)) : ?>
            <div class="alert alert-success"><?php echo $updateStatus; ?></div>
        <?php endif; ?>
    </div>
  
</body>

</html>

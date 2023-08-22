<?php
// Start the session
session_start();

// Check if user is already logged in, redirect to dashboard if true
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

// Include database connection
require_once 'config.php';

// Define variables and set to empty values
$username = $password = '';
$usernameErr = $passwordErr = '';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST['username'])) {
        $usernameErr = 'Username is required';
    } else {
        $username = $_POST['username'];
    }

    // Validate password
    if (empty($_POST['password'])) {
        $passwordErr = 'Password is required';
    } else {
        $password = $_POST['password'];
    }

    // Check if username and password are valid
    if (empty($usernameErr) && empty($passwordErr)) {
        // Prepare a SELECT statement
        $sql = "SELECT id, username FROM users WHERE username = ? AND password = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $username, $password);

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Store the result
                $stmt->store_result();

                // Check if username and password exist in the database
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username);

                    // Fetch the result
                    if ($stmt->fetch()) {
                        // Set session variables
                        $_SESSION['id'] = $id;
                        $_SESSION['username'] = $username;

                        if ($username === 'admin') {
                            // Redirect to the admin dashboard
                            header("Location: admin_dashboard.php");
                            exit;
                        } else {
                            // Redirect to the regular user dashboard
                            header("Location: dashboard.php");
                            exit;
                        }
                    }
                } else {
                    $passwordErr = 'Invalid username or password';
                }
            } else {
                echo 'Oops! Something went wrong. Please try again later.';
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
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            width: 500px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Form fields -->
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control">
                <span class="text-danger"><?php echo $usernameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control">
                <span class="text-danger"><?php echo $passwordErr; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Login" class="btn btn-primary">
            </div>  
        </form>
        <p>Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</body>

</html>

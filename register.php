<?php
// Start the session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is already logged in, redirect to dashboard if true
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

// Include database connection
require_once 'config.php';

// Define variables and set to empty values
$fname = $lname = $username = $phone = $password = $confirmPassword = '';
$fnameErr = $lnameErr = $usernameErr = $phoneErr = $passwordErr = $confirmPasswordErr = '';

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty($_POST['fname'])) {
        $fnameErr = 'First name is required';
    } else {
        $fname = $_POST['fname'];
        // Check if name contains only letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed";
        }
    }

    // Validate last name
    if (empty($_POST['lname'])) {
        $lnameErr = 'Last name is required';
    } else {
        $lname = $_POST['lname'];
        // Check if name contains only letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/", $lname)) {
            $lnameErr = "Only letters and white space allowed";
        }
    }

    // Validate username
    if (empty($_POST['username'])) {
        $usernameErr = 'Username is required';
    } else {
        $username = $_POST['username'];

        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variable to the prepared statement as parameter
            $stmt->bind_param("s", $username);

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
        // Check if phone number is valid
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $phoneErr = 'Invalid phone number';
        }
    }

    // Validate password
    if (empty($_POST['password'])) {
        $passwordErr = 'Password is required';
    } else {
        $password = $_POST['password'];

        // Password strength validation
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[^A-Za-z0-9]/', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            $passwordErr = 'password is not strong enough';
        }
    }

    // Validate confirm password
    if (empty($_POST['confirm_password'])) {
        $confirmPasswordErr = 'Confirm password is required';
    } else {
        $confirmPassword = $_POST['confirm_password'];
        if ($password !== $confirmPassword) {
            $confirmPasswordErr = 'Passwords do not match';
        }
    }

    // Check if there are no errors, insert new user into database
    if (empty($fnameErr) && empty($lnameErr) && empty($usernameErr) && empty($phoneErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        // Prepare an INSERT statement
        $sql = "INSERT INTO users (fname, lname, username, phone, password) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssss", $fname, $lname, $username, $phone, $password);

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Registration successful, redirect to login page
                header("Location: index.php");
                exit;
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
    <title>Register</title>
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
        padding-top: -30px;
        padding: 20px;
        background: #f5f5f5;
        border-radius: 15px;
        overflow-y: auto;
    }
    form{
        height: 600px;
    }
    </style>
</head>

<body>
    <div class="form-container">     
        <h2>Register</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
            <!-- Form fields -->
            <div class="form-group">
                <label>First name:</label>
                <input type="text" name="fname" class="form-control" placeholder="Enter your first name">
                <span class="text-danger"><?php echo $fnameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Last name:</label>
                <input type="text" name="lname" class="form-control" placeholder="Enter your last name">
                <span class="text-danger"><?php echo $lnameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control"  placeholder="Choose a username">
                <span class="text-danger" id="phoneError"><?php echo $usernameErr; ?></span>
            </div>
            <div class="form-group">
                <label>Phone number:</label>
                <input type="tel" name="phone" class="form-control" placeholder="0XXXXXXXXX">
                <span class="text-danger"><?php echo $phoneErr; ?></span>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" placeholder="Enter a password">
                <span class="text-danger"><?php echo $passwordErr; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password">
                <span class="text-danger"><?php echo $confirmPasswordErr; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" class="btn btn-primary">
            </div>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
    
</body>

</html>

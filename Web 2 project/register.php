<?php
require('connect.php'); // Include your database connection script

$servername = "localhost"; // Replace with your database server
$username = "serveruser"; // Replace with your database username
$password = "gorgonzola7!"; // Replace with your database password
$dbname = "serverside"; // Replace with your database name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; // Initialize the error message
$submittedUsername = isset($_POST['username']) ? $_POST['username'] : ''; // Retrieve submitted username

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    // Check if the username already exists
    if (isUsernameExists($conn, $username)) {
        $error_message = "Username already exists. Please choose a different username.";
    } elseif ($password === $confirmPassword) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute an SQL query to insert data into the 'users' table
        $sql = "INSERT INTO user (Username, Password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $passwordHash, $email);

        if ($stmt->execute()) {
            // Registration successful, you can redirect to a login page or other actions
            header("Location: login.php");
            exit();
        } else {
            echo "Registration failed: " . $stmt->error;
        }
    } else {
        $error_message = "Passwords do not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Registration</title>
    <link rel="stylesheet" href="Styles.css">
</head>

<body>
    <h2>Register</h2>
    <ul>
        <li><a href="index.php">Home</a></li>
    </ul>
    <?php
    if (!empty($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
    }
    ?>
    <form method="post" action="register.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required
            value="<?= htmlspecialchars($submittedUsername); ?>" placeholder="Enter your username">
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required value="<?php echo isset($email) ? $email : ''; ?>" placeholder="Enter your email">
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <!-- Password visibility toggle -->
        <div id="password-toggle" onclick="togglePasswordVisibility('password')">
            <img src="eye-open.png" alt="Toggle Password Visibility" width="20" height="20">
        </div>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <!-- Confirm Password visibility toggle -->
        <div id="confirm-password-toggle" onclick="togglePasswordVisibility('confirm_password')">
            <img src="eye-open.png" alt="Toggle Confirm Password Visibility" width="20" height="20">
        </div>
        <br>
        <input type="submit" name="register" value="Register">
    </form>

    <script>
        function togglePasswordVisibility(inputId) {
            var passwordInput = document.getElementById(inputId);
            var passwordToggle = document.getElementById("password-toggle");
            var confirmToggle = document.getElementById("confirm-password-toggle");

            // Toggle the type attribute of the password input
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.innerHTML = '<img src="eye-closed.png" alt="Toggle Password Visibility" width="20" height="20">';
                if (inputId === "confirm_password") {
                    confirmToggle.innerHTML = '<img src="eye-closed.png" alt="Toggle Confirm Password Visibility" width="20" height="20">';
                }
            } else {
                passwordInput.type = "password";
                passwordToggle.innerHTML = '<img src="eye-open.png" alt="Toggle Password Visibility" width="20" height="20">';
                if (inputId === "confirm_password") {
                    confirmToggle.innerHTML = '<img src="eye-open.png" alt="Toggle Confirm Password Visibility" width="20" height="20">';
                }
            }
        }
    </script>
</body>

</html>

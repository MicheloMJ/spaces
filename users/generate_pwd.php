<?php
require "../dbconn.php";
session_start();

// Check if user is logged in with OTP
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    // Validate password
    if (isValidPassword($password)) {
        // Insert password into users table
        $email = $_SESSION['email'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // Password inserted successfully
            // Redirect to index.php
            header("Location: index.php");
            exit;
        } else {
            // Error inserting password
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Password does not meet the requirements.";
    }
}

// Password validation function
function isValidPassword($password) {
    // Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit
    return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/', $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Password</title>
</head>
<body>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Generate Password</button>
    </form>
    <script>
        var passwordInput = document.getElementById("password");
        passwordInput.addEventListener('input', function() {
            var password = passwordInput.value;
            if (isValidPassword(password)) {
                passwordInput.setCustomValidity('');
            } else {
                passwordInput.setCustomValidity('Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit');
            }
        });

        function isValidPassword(password) {
            return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/.test(password);
        }
    </script>
</body>
</html>

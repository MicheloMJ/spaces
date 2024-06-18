<?php
// ! DO NOT DELETE
require "../dbconn.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['otp'])) {
        // * Get OTP
        $input_otp = filter_var($_POST['otp'], FILTER_SANITIZE_NUMBER_INT);
        if ($input_otp == $_SESSION['otp']) {
            // Todo: Sanitize the content
            $email = $_SESSION['email'];
            $first_name = $_SESSION['first_name'];
            $last_name = $_SESSION['last_name'];

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (email, first_name, last_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $first_name, $last_name);

            if ($stmt->execute()) {
                // Success
                unset($_SESSION['otp']);
                echo "<script>alert('Registration successful!'); window.location.href = 'index.php';</script>";
            } else {
                // Database error
                echo "<script>alert('Database error: " . $conn->error . "'); window.location.href = '#';</script>";
            }
        } else {
            // OTP is incorrect
            echo "<script>alert('Invalid OTP. Please try again.'); window.location.href = '#';</script>";
        }
    } else {
        // Generate OTP
        $_SESSION['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $_SESSION['first_name'] = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
        $_SESSION['last_name'] = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);

        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        // Display OTP for debugging
        $otpMessage = "Your OTP code is $otp";
        echo "<div id='otp-display' style='position: fixed; bottom: 10px; right: 10px; background-color: black; color: white; padding: 10px; border-radius: 5px; opacity: 0;'>$otpMessage</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="icon" type="image/png" href="<?php echo $FAVICON_URL; ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 100%;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        #otp-display {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #000;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        #otp-display.show {
            opacity: 1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <form action="generate_otp.php" method="POST">
                <label for="otp">Enter OTP:</label>
                <input type="text" id="otp" name="otp" required>
                <button type="submit">Verify OTP</button>
            </form>
        </div>
    </div>

    <div id="otp-display" class="<?php if (isset($_SESSION['otp'])) echo 'show'; ?>">
        Your OTP code is <?php echo $_SESSION['otp']; ?>
    </div>

    <script>
        // Function to fade in the OTP display
        function fadeIn(element, duration) {
            element.style.opacity = 0;
            element.classList.add('show');
            let opacity = 0;
            const interval = 50;
            const increment = interval / duration;

            function step() {
                opacity += increment;
                if (opacity <= 1) {
                    element.style.opacity = opacity;
                    requestAnimationFrame(step);
                }
            }

            step();
        }

        // Get the OTP display element and fade it in
        const otpDisplay = document.getElementById('otp-display');
        if (otpDisplay && !otpDisplay.classList.contains('show')) {
            fadeIn(otpDisplay, 1000); // 1 second duration for the fade-in effect
        }
    </script>
</body>

</html>

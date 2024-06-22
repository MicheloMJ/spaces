<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'config.php';
require '../dbconn.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // Assuming password is not hashed
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['space_id'] = $user['space_id']; // Set space_id in session
            $_SESSION['email'] = $user['email']; // Set user's email in session
            header("Location: admin_account.php");
            exit;
        } else {
            $errors[] = "Invalid login credentials";
        }
    } else {
        $errors[] = "Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" type="image/png" href="<?php echo $FAVICON_URL; ?>">
    <link rel="stylesheet" href="css/index.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: black;
        }
        .login-container {
            max-width: 300px;
            height: 350px;
            position: relative;
            z-index: 2;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            color: black;
        }
        .login-container input[type="email"],
        .login-container input[type="password"] {
            padding: 16px;
            font-size: 16px;
            color: black;
            background-color: white;
            border: 1px solid gray;
            border-radius: 5px;
        }
        .login-container input[type="email"]:focus,
        .login-container input[type="password"]:focus {
            border-color: turquoise;
            outline: none;
            transition: ease-in-out .4s;
        }
        .login-container button[type="submit"] {
            background-color: black;
            border-color: black;
            color: white;
            padding: 8px 16px;
            font-size: 12px;
        }
        .login-container button[type="submit"]:hover {
            background-color: dodgerblue;
            transition: ease-in-out .4s;
            cursor: pointer;
        }
        .login-container button[type="submit"]:focus {
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.5);
        }
        .login-container a {
            color: black;
        }
        .login-container a:hover {
            text-decoration: underline;
            transition: ease-in-out .4s;
            color: gray;
        }
        .login-container p:hover{
            text-decoration: none;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: transparent;
            text-align: center;
            color: black;
        }
        #particles-js {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: 50% 50%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
        }
    </style>
</head>
<body>
<div class="flex justify-center items-center h-screen">
    <div class="login-container rounded-lg shadow-md">
        <div class="mb-6 flex justify-center items-center">
            <img src="img/spaces.png" alt="Spaces Logo">
        </div>
        <?php if (!empty($errors)): ?>
            <div class="text-red-500 mb-4">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form id="login-form" method="post" action="index.php">
            <div class="mb-6">
                <input type="email" id="email" name="email" placeholder="space mail"
                    class="mt-1 block w-full px-3 py-2 rounded-md shadow-sm focus:ring-turquoise-500 focus:border-turquoise-500 text-lg text-black">
            </div>
            <div class="mb-6">
                <input type="password" id="password" name="password" placeholder="space password"
                    class="mt-1 block w-full px-3 py-2 rounded-md shadow-sm focus:ring-turquoise-500 focus:border-turquoise-500 text-lg text-black">
            </div>
            <div class="flex justify-between items-center mb-8">
                <a href="#" class="text-m text-black hover:underline">Forgot your password?</a>
                <button type="submit" id="loginBtn"
                    class="px-4 py-2 bg-black text-white rounded-md hover:bg-turquoise focus:outline-none focus:bg-turquoise"
                    disabled>Login</button>
            </div>
        </form>
        <div class="text-center">
            <p class="text-m text-black hover:underline">Login as a standard&nbsp;<a href="../users/index.php"
                    class="text-black hover:underline">User</a></p>
        </div>
        <div class="text-center">
            <p class="text-m mt-10 text-gray-600 hover:underline">powered by NATEC</p>
        </div>
    </div>
</div>
<div id="particles-js"></div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');

        emailInput.addEventListener('input', toggleLoginBtn);
        passwordInput.addEventListener('input', toggleLoginBtn);

        function toggleLoginBtn() {
            if (emailInput.value.trim() !== '' && passwordInput.value.trim() !== '') {
                loginBtn.disabled = false;
            } else {
                loginBtn.disabled = true;
            }
        }
    });

    $.getScript("https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js", function () {
        particlesJS('particles-js',
            {
                "particles": {
                    "number": {
                        "value": 80,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#fff"
                    },
                    "shape": {
                        "type": "circle",
                        "stroke": {
                            "width": 0,
                            "color": "#ffffff"
                        },
                        "polygon": {
                            "nb_sides": 5
                        },
                        "image": {
                            "width": 100,
                            "height": 100
                        }
                    },
                    "opacity": {
                        "value": 0.5,
                        "random": false,
                        "anim": {
                            "enable": false,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 5,
                        "random": true,
                        "anim": {
                            "enable": false,
                            "speed": 40,
                            "size_min": 0.1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#ffffff",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 6,
                        "direction": "none",
                        "random": false,
                        "straight": false,
                        "out_mode": "out",
                        "attract": {
                            "enable": false,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "repulse"
                        }
                        },
                        "modes": {
                            "grab": {
                                "distance": 400,
                                "line_linked": {
                                    "opacity": 1
                                }
                            },
                            "bubble": {
                                "distance": 400,
                                "size": 40,
                                "duration": 2,
                                "opacity": 8,
                                "speed": 3
                            },
                            "repulse": {
                                "distance": 200
                            },
                            "push": {
                                "particles_nb": 4
                            },
                            "remove": {
                                "particles_nb": 2
                            }
                        }
                    },
                    "retina_detect": true,
                    "config_demo": {
                        "hide_card": false,
                        "background_color": "#ffffff",
                        "background_image": "",
                        "background_position": "50% 50%",
                        "background_repeat": "no-repeat",
                        "background_size": "cover"
                    }
                }
            );
    });
</script>
</body>
</html>

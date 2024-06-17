<?php

require "../dbconn.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Spaces</title>
    <?php require 'config.php'; ?>
    <link rel="icon" type="image/png" href="<?php echo $FAVICON_URL; ?>">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
            color: black;
        }

        .join-container {
            max-width: 450px;
            position: relative;
            z-index: 2;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            color: black;
        }

        .join-container input[type="email"],
        .join-container input[type="text"] {
            padding: 16px;
            font-size: 16px;
            color: black;
            background-color: white;
            border: 1px solid gray;
            border-radius: 5px;
        }

        .join-container input[type="email"]:focus,
        .join-container input[type="text"]:focus {
            border-color: turquoise;
            outline: none;
            transition: ease-in-out .4s;
        }

        .join-container button[type="submit"] {
            background-color: dodgerblue;
            border-color: dodgerblue;
            padding: 12px 24px;
            font-size: 16px;
            color: #fff;
        }

        .join-container button[type="submit"]:hover {
            background-color: #000;
            transition: ease-in-out .4s;
            cursor: pointer;
        }

        .join-container button[type="submit"]:focus {
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.5);
        }

        .join-container a {
            color: black;
        }

        .join-container a:hover {
            text-decoration: underline;
            transition: ease-in-out .4s;
            color: gray;
        }
        .join-container p:hover{
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
            top: 0px;
            left: 0px;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="flex justify-center items-center h-screen">
        <div class="join-container rounded-lg shadow-md">
            <!-- Logo -->
            <div class="mb-6 flex justify-center items-center">
                <img src="img/spaces.png" alt="Spaces Logo">
            </div>

            <!-- Join Form -->
            <form action="generate_otp.php" method="POST">
                <div class="mb-6">
                    <input type="email" id="email" name="email" placeholder="Email Address"
                        class="mt-1 block w-full px-3 py-2 rounded-md shadow-sm focus:ring-turquoise-500 focus:border-turquoise-500 text-lg text-black" required>
                </div>

                <div class="mb-6">
                    <input type="text" id="first_name" name="first_name" placeholder="First Name"
                        class="mt-1 block w-full px-3 py-2 rounded-md shadow-sm focus:ring-turquoise-500 focus:border-turquoise-500 text-lg text-black" required>
                </div>

                <div class="mb-6">
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name"
                        class="mt-1 block w-full px-3 py-2 rounded-md shadow-sm focus:ring-turquoise-500 focus:border-turquoise-500 text-lg text-black" required>
                </div>

                <div class="flex justify-center items-center mb-8">
                    <button type="submit" id="generateOTPBtn"
                        class="px-4 py-2 bg-dodgerblue text-black rounded-md hover:bg-turquoise focus:outline-none focus:bg-turquoise"
                        disabled>Generate OTP</button>
                </div>
            </form>

            <div class="text-center">
                <p class="text-m text-black hover:underline">Already have an account? &nbsp;<a href="index.php"
                        class="text-black hover:underline">Log in</a></p>
            </div>
        </div>
    </div>

    <div id="particles-js"></div>

    <?php require "footer.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            const firstNameInput = document.getElementById('first_name');
            const lastNameInput = document.getElementById('last_name');
            const generateOTPBtn = document.getElementById('generateOTPBtn');

            [emailInput, firstNameInput, lastNameInput].forEach(function (input) {
                input.addEventListener('input', function () {
                    if (emailInput.value && firstNameInput.value && lastNameInput.value) {
                        generateOTPBtn.disabled = false;
                    } else {
                        generateOTPBtn.disabled = true;
                    }
                });
            });
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
                            "value": "#000" /* Black color for particles */
                        },
                        "shape": {
                            "type": "circle",
                            "stroke": {
                                "width": 0,
                                "color": "#000000"
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
                            },
                            "onclick": {
                                "enable": true,
                                "mode": "push"
                            },
                            "resize": true
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

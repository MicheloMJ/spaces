<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if(isset($_SESSION['user_id'])) {
    require 'config.php';
    require '../dbconn.php';
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        // Redirect to index.php if user not found
        header("Location: index.php");
        exit;
    }
} else {
    // Redirect to index.php if session not set
    header("Location: index.php");
    exit;
}
// Fetch available spaces from the database
$sql_spaces = "SELECT * FROM spaces";
$result_spaces = $conn->query($sql_spaces);
$spaces = [];
if ($result_spaces->num_rows > 0) {
    while ($row = $result_spaces->fetch_assoc()) {
        $spaces[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Space</title>
    <!-- Add Bootstrap CSS -->
    <link rel="icon" type="image/png" href="<?php echo $FAVICON_URL; ?>">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add your favorite icon library CDN here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .nav-bar {
            background-color: #f8f9fa;
            border: 2px solid #fff;
            padding: 5px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999; /* Ensure top navigation bar remains visible */
        }

        .nav-bar .navbar-brand {
            margin-left: 20px;
            font-size: 20px; /* Adjust font size as needed */
        }

        .nav-bar .nav-link {
            color: #495057;
            font-weight: bold;
            text-align: center;
            font-size: 15px; /* Adjust font size as needed */
        }

        .nav-bar .nav-link .fas {
            /* display: block; */
            font-size: 15px;
            margin-bottom: 5px;
        }

        @media (max-width: 576px) {
            .nav-bar {
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }

            .nav-bar .nav-item {
                display: inline-block;
                float: none;
            }
        }
        
        /* Style for the side navs */
        .side-nav {
            /* border: 2px solid #fff; */
            position: fixed;
            top: 50px; /* Adjust based on the height of the top navbar */
            width: 250px;
            height: calc(100% - 90px); /* Adjust to account for top navbar height */
            overflow-y: auto;
            background-color: #f8f9fa;
            border-right: 1px solid #fff;
            padding: 20px;
            z-index: 998; /* Ensure side nav appears below top navbar */
        }
        
        
        /* Style for the search field */
        .search-field {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .space-item {
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.space-item h5 {
    font-size: 16px;
    margin-bottom: 5px;
}

.space-item p {
    font-size: 14px;
    margin-bottom: 10px;
}

.space-item button {
    font-size: 14px;
    padding: 5px 10px;
}

    </style>
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light nav-bar">
        <a class="navbar-brand" href="#">
            <!-- Logo -->
            <div class="mb-6 flex justify-center items-center">
            Private Messaging
                <!-- <img src="img/spaces.png" alt="Spaces Logo" width="50%"> -->
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-user mr-1"></i>
                        <?php echo $user['first_name']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-cog mr-1"></i>
                        Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Left Side Nav for Joining Spaces -->
    <div class="side-nav">
        <h5>Join Spaces</h5>
        <!-- Content for left side nav here -->
        <ul class="nav flex-column">
        <input type="text" class="form-control mb-3" id="search" placeholder="Search for spaces">
    <?php if (!empty($spaces)) : ?>
        <?php foreach ($spaces as $space) : ?>
            <div class="space-item">
                <h5><?php echo $space['name']; ?></h5>
                <p><?php echo $space['description']; ?></p>
                <button class="btn btn-primary">Join</button>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No spaces available to join.</p>
    <?php endif; ?>
        </ul>
    </div>
    
    <!-- Center Content for My Spaces -->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
            $(document).ready(function() {
        // Implement search functionality
        $('#search').keyup(function() {
            var searchText = $(this).val().toLowerCase();
            $('.space-item').each(function() {
                var spaceName = $(this).find('h5').text().toLowerCase();
                var spaceDescription = $(this).find('p').text().toLowerCase();
                if (spaceName.includes(searchText) || spaceDescription.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    </script>
</body>

</html>

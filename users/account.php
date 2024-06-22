
<?php 
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['user_id'])) {
    require 'config.php';
    require '../dbconn.php';

    $user_id = $_SESSION['user_id'];

    // Fetch user details
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

    // Handle join request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['join_space_id'])) {
        $space_id = intval($_POST['join_space_id']);
        
        // Check if the user is already requesting to join this space
        $sql = "SELECT * FROM user_spaces WHERE user_id = ? AND space_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $space_id);
        $stmt->execute();
        $existing_request_result = $stmt->get_result();

        if ($existing_request_result->num_rows == 0) {
            // Insert a new join request with 'pending' status
            $sql = "INSERT INTO user_spaces (user_id, space_id, status, request_date) VALUES (?, ?, 'pending', CURRENT_TIMESTAMP)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $space_id);
            $stmt->execute();

            // Redirect to refresh the page
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Handle search
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_submit'])) {
        $search_query = "%" . $_POST['search_query'] . "%"; // Get search query from form
        
        // Search for spaces
        $sql = "SELECT * FROM spaces WHERE name LIKE ? OR description LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search_query, $search_query);
        $stmt->execute();
        $spaces_result = $stmt->get_result();
        $spaces = $spaces_result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Fetch all spaces if no search query
        $sql = "SELECT * FROM spaces";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $spaces_result = $stmt->get_result();
        $spaces = $spaces_result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch joined spaces
    $sql = "SELECT spaces.id as space_id, spaces.name, spaces.description, user_spaces.status, user_spaces.request_date
            FROM spaces 
            INNER JOIN user_spaces ON spaces.id = user_spaces.space_id 
            WHERE user_spaces.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $joined_spaces_result = $stmt->get_result();
    $joined_spaces = $joined_spaces_result->fetch_all(MYSQLI_ASSOC);

    // Handle withdraw request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw-request'])) {
        // Get the space ID from the form
        $space_id = intval($_POST['withdraw_space_id']);

        // Delete the join request for the current user and space from the database
        $sql = "DELETE FROM user_spaces WHERE user_id = ? AND space_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $space_id);
        $stmt->execute();

        // Redirect to refresh the page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

} else {
    // Redirect to index.php if session not set
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Space</title>
    <link rel="icon" type="image/png" href="<?php echo $FAVICON_URL; ?>">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            z-index: 999;
        }

        .nav-bar .navbar-brand {
            margin-left: 20px;
            font-size: 20px;
        }

        .nav-bar .nav-link {
            color: #495057;
            font-weight: bold;
            text-align: center;
            font-size: 15px;
        }

        .nav-bar .nav-link .fas {
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

        .side-nav {
            position: fixed;
            top: 55px;
            width: 250px;
            height: calc(100% - 55px);
            overflow-y: auto;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            padding: 20px;
            z-index: 998;
        }

        .main-content {
            margin-left: 270px;
            max-width: calc(100% - 270px);
            padding: 20px;
            margin-top: 55px;
        }

        .button-container {
            display: flex;
            gap: 5px; /* Space between buttons */
            margin-top: 10px;
        }

        .button-container button {
            font-size: 12px;
            padding: 6px 10px;
            transition: background-color 0.3s ease-in-out;
        }

        .button-container button.btn-primary {
            background-color: #20c997; /* Turquoise */
            border-color: #20c997;
            color: #fff;
        }

        .button-container button.btn-primary:hover {
            background-color: #17a2b8; /* Darker turquoise on hover */
        }

        .button-container button.btn-primary:disabled {
            background-color: #6c757d; /* Dark gray when disabled */
            border-color: #6c757d;
        }

        .button-container button.btn-secondary {
            background-color: #dc3545; /* Red */
            border-color: #dc3545;
            color: #fff;
        }

        .button-container button.btn-secondary:hover {
            background-color: #c82333; /* Darker red on hover */
        }

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
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }

        .space-item h5 {
            margin-bottom: 10px;
        }

        .pending-approval {
            background-color: #f9f9f9; /* Light gray */
            border-color: #ccc;
        }

        .approval-message {
            color: #dc3545; /* Red */
            margin-top: 10px;
            animation: blink-animation 60s infinite;
        }

        @keyframes blink-animation {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light nav-bar">
        <a class="navbar-brand" href="#">My Space</a>
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

    <div class="side-nav">
    <h5>Join Spaces</h5>
    <input type="text" class="search-field" placeholder="Search for spaces" id="searchInput">
    <ul class="nav flex-column" id="spacesList">
        <?php if (!empty($spaces)) : ?>
            <?php foreach ($spaces as $space) : ?>
                <div class="space-item" data-name="<?php echo strtolower($space['name']); ?>" data-description="<?php echo strtolower($space['description']); ?>">
                    <h5><?php echo $space['name']; ?></h5>
                    <p><?php echo $space['description']; ?></p>
                    <form method="post" action="">
                        <input type="hidden" name="join_space_id" value="<?php echo $space['id']; ?>">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Requesting approval to join this space.');">Join</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No spaces available to join.</p>
        <?php endif; ?>
    </ul>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const spacesList = document.getElementById("spacesList").querySelectorAll(".space-item");

        searchInput.addEventListener("input", function() {
            const searchTerm = searchInput.value.toLowerCase();
            spacesList.forEach(function(space) {
                const name = space.getAttribute("data-name");
                const description = space.getAttribute("data-description");
                if (name.includes(searchTerm) || description.includes(searchTerm)) {
                    space.style.display = "block";
                } else {
                    space.style.display = "none";
                }
            });
        });
    });
</script>

    <div class="main-content">
    <h2>Active Spaces</h2>
    <?php if (!empty($joined_spaces)) : ?>
        <?php foreach ($joined_spaces as $space) : ?>
            <div class="space-item <?php echo ($space['status'] == 'pending') ? 'pending-approval' : ''; ?>">
                <h5><?php echo $space['name']; ?></h5>
                <p><?php echo $space['description']; ?></p>
                <?php if ($space['status'] == 'pending') : ?>
                    <div class="button-container">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
                            <?php 
                                // Check if 'request_date' key exists and is not null
                                if (isset($space['request_date']) && $space['request_date'] !== null) {
                                    $request_time = strtotime($space['request_date']);
                                    $current_time = time();
                                    $time_difference = $current_time - $request_time;
                                    $hours_difference = $time_difference / (60 * 60);
                                    $disable_re_request = ($hours_difference < 72) ? 'disabled' : '';
                                } else {
                                    $disable_re_request = ''; // Default value if 'request_date' is not set or null
                                }
                            ?>
                            <input type="hidden" name="space_id" value="<?php echo $space['space_id']; ?>">
                            <button type="submit" class="btn btn-primary" name="Resubmit Request" <?php echo $disable_re_request; ?>>Resubmit Request</button>
                        </form>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
                            <input type="hidden" name="withdraw_space_id" value="<?php echo $space['space_id']; ?>">
                            <button type="submit" class="btn btn-danger" name="withdraw-request">Withdraw request</button>
                        </form>
                    </div>
                    <div class="approval-message">Waiting for approval</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>You have not joined any spaces.</p>
    <?php endif; ?>
</div>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

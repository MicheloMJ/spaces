<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require '../dbconn.php';

// Assuming the admin's space ID is stored in the session
$admin_space_id = $_SESSION['space_id'];

// Fetch the name of the space
$sql_space_name = "SELECT name FROM spaces WHERE id = ?";
$stmt_space_name = $conn->prepare($sql_space_name);
$stmt_space_name->bind_param("i", $admin_space_id);
$stmt_space_name->execute();
$stmt_space_name->bind_result($space_name);
$stmt_space_name->fetch();
$stmt_space_name->close();

// Function to approve a request
if (isset($_POST['action']) && $_POST['action'] == 'approve') {
    $user_id = $_POST['user_id'];
    $space_id = $_POST['space_id'];

    // Perform the logic to approve the request (e.g., update user_spaces table)
    $sql = "UPDATE user_spaces SET approved = 1 WHERE user_id = ? AND space_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $space_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Request approved successfully']);
    exit;
}

// Function to delete a request
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $user_id = $_POST['user_id'];
    $space_id = $_POST['space_id'];

    // Perform the logic to delete the request (e.g., delete from user_spaces table)
    $sql = "DELETE FROM user_spaces WHERE user_id = ? AND space_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $space_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Request deleted successfully']);
    exit;
}

// Fetch users who have requested to join the admin's space
$sql = "SELECT users.id AS user_id, users.email, users.first_name, users.last_name, spaces.id AS space_id, spaces.name AS space_name, user_spaces.request_date
        FROM users
        INNER JOIN user_spaces ON users.id = user_spaces.user_id
        INNER JOIN spaces ON user_spaces.space_id = spaces.id
        WHERE spaces.id = ?
        ORDER BY user_spaces.request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_space_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "$space_name"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }

        .approve-btn,
        .delete-btn {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .approve-btn {
            background-color: #4CAF50; /* Green */
        }

        .approve-btn:hover {
            background-color: #45a049; /* Darker green */
        }

        .delete-btn {
            background-color: #f44336; /* Red */
        }

        .delete-btn:hover {
            background-color: #d32f2f; /* Darker red */
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .alert.success {
            background-color: orange;
            color: #fff;
        }

        .alert.error {
            background-color: #f44336;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php require "navbar.php";?>
    <div class="container">
        <h1><?php echo "$space_name Admin"; ?></h1>
        <h4>Requests to Join my Spaces</h4>
        <div id="alert-container"></div>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>User Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Space Name</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr data-user-id="<?php echo $row['user_id']; ?>" data-space-id="<?php echo $row['space_id']; ?>">
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['space_name']; ?></td>
                            <td><?php echo $row['request_date']; ?></td>
                            <td>
                                <button class="approve-btn">Approve</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No requests to join my spaces.</p>
        <?php endif; ?>
    </div>

    <!-- Include your JavaScript code here -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alertContainer = document.getElementById('alert-container');
            const approveButtons = document.querySelectorAll('.approve-btn');
            const deleteButtons = document.querySelectorAll('.delete-btn');

            approveButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const userId = row.getAttribute('data-user-id');
                    const spaceId = row.getAttribute('data-space-id');
                    handleRequest('approve', userId, spaceId, row);
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const userId = row.getAttribute('data-user-id');
                    const spaceId = row.getAttribute('data-space-id');
                    handleRequest('delete', userId, spaceId, row);
                });
            });

            function handleRequest(action, userId, spaceId, row) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'admin_account.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        showAlert(response.message, response.status === 'success' ? 'success' : 'error');
                        if (response.status === 'success') {
                            row.remove();
                        }
                    } else {
                        showAlert('An error occurred while processing the request', 'error');
                    }
                };
                xhr.send(`action=${action}&user_id=${userId}&space_id=${spaceId}`);
            }

            function showAlert(message, type) {
                const alert = document.createElement('div');
                alert.className = `alert ${type}`;
                alert.textContent = message;
                alertContainer.appendChild(alert);
                setTimeout(() => {
                    alert.remove();
                }, 3000);
            }
        });
    </script>
</body>

</html>

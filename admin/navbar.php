<?php
// Assuming the user's name is stored in the session
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';

// You can add more links or options as needed
?>

<style>
    /* Basic styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

nav {
    background-color: #fff;
    color: #333;
    padding: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    text-align: center;
}

nav ul li {
    display: inline-block;
    margin-right: 20px;
}

nav ul li:last-child {
    margin-right: 0;
}

nav ul li a {
    color: #333;
    text-decoration: none;
    padding: 10px;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
}

nav ul li a:hover {
    background-color: #f0f0f0;
}

.nav-icon {
    margin-right: 5px;
}

/* Responsive styling */
@media only screen and (max-width: 600px) {
    nav ul li {
        display: block;
        margin: 10px 0;
    }

    nav ul li:last-child {
        margin-bottom: 0;
    }
}

</style>

<nav class="navbar navbar-light">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">Welcome, <?php echo $user_name; ?></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-pencil nav-icon"></i>Edit Tags</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-gear nav-icon"></i>Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right nav-icon"></i>Logout</a></li>
        </ul>
    </nav>
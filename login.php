<?php
session_start();

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'form';

// Create a database connection
$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_error()) {
    exit('Error connecting to the database: ' . mysqli_connect_error());
}

if (!isset($_POST['username'], $_POST['password'])) {
    exit('Empty username or password');
}

// Check if the submitted username and password are not empty
if (empty($_POST['username']) || empty($_POST['password'])) {
    exit('Username or password is empty');
}

// Prepare a SELECT query to fetch user data
if ($stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();

        // Verify the submitted password against the stored hashed password
        if (password_verify($_POST['password'], $password)) {
            // Password is correct, create a session for the user
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['user_id'] = $id;

            echo 'Login successful. Welcome, ' . $_POST['username'] . '!';
        } else {
            echo 'Incorrect password';
        }
    } else {
        echo 'Username not found';
    }

    $stmt->close();
} else {
    echo 'Error occurred';
}

$conn->close();
?>

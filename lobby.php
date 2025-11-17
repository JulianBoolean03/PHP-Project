<?php
session_start();

$errorMessage = $_GET['error'] ?? '';

$username = $_POST['username'] ?? '';

//If username has not been created yet
if (!isset($_SESSION['usernames'])) {
    $_SESSION['usernames'] = [];
}

//Did user enter a username and entered it in index?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Check if username already exists
    if(in_array($username, $_SESSION['usernames'])) {
        header("Location: index.php?error=" . urlencode("Username already exists! Please enter a different one!"));
        exit;
    }

//Add username to session list
$_SESSION['usernames'][] = $username;

//Save current user
$_SESSION['Userdata']['Username'] = $username;
}

//If user accesses this page w/o entering a username first
if (!isset($_SESSION['Userdata']['Username'])) {
    header("Location: index.php?error=" . urlencode("Please enter a username."));
}

//Retrieve current username
$current_user = $_SESSION['UserData']['USername'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Lobby</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class = "page-wrapper">
    <header class = "header">
        <h1 class = "title">Jeopardy Game Show</h1>
        <p class = "subtitle">Welcome <?= htmlspecialchars($current_user) ?>!</p>
    </header>

    <main class = "card">
        <h2 class="card-title">Welcome to the Lobby!</h2>
        <p>Please wait while we match you to a game...</p>
    </main>

    <footer class="footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
</div>
</body>
</html>
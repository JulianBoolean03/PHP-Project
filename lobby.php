<?php
$errorMessage = $_GET['error'] ?? '';

session_start();
if (isset($_POST['Submit'])) {
    $login = array(); //Used to store existing usernames. Will fix later

    $username = isset($_POST['Username']) ? $_POST['Username'] : '';

    //Check if username already exists
    if (isset($login[$username])) {
        $_SESSION['Userdata']['Username']=$login[$username];
        header("location:lobby.php");
        exit;
    } else {
        //Username does not exist in array
        echo htmlspecialchars($errorMessage);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Homepage</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class = "page-wrapper">
    <header class = "header">
        <h1 class = "title">Jeopardy Game Show</h1>
        <p class = "subtitle">Welcome <?= $username['Username'] ?>!</p>
    </header>

    <main class = "card">
        <h2 class="card-title">Welcome to the Lobby!</h2>
    </main>
</div>
</body>
</html>
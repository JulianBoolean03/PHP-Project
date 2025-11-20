<?php

session_start();

$username = $_SESSION['username'] ?? 'Player1';
$score = ''; //User score

//User wants to play again
if (isset($POST['$play-again'])) {
    header("Location: lobby.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Winner</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page-wrapper">
    <header class="header">
        <h1 class="title">JEOPARDY GAME SHOW</h1>
        <p class="subtitle">Game Over!</p>
        <p class="subtitle-small">Logged in as <?php echo htmlentities($username); ?></p>
    </header>

    <main class="card">
        <h2 class="card-winner">Winner: <?php echo htmlspecialchars($username) ?> </h2>
        <p>Score: </p>
    </main>

    <!-- Button to Play Again that leads to a new game -->
     <form method="post">
        <button class="btn-primary" name="play-again">Play Again</button>
     </form>

    <footer class="footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
</div>
</body>
</html>
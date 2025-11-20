<?php

session_start();

/*
$_SESSION['players'] = [
    'Player1' => 600
]
Array for number of players & their scores (to be updated)
*/

//If players don't exist
$players = $_SESSION['players'] ?? ['Player1' => 0];

//Determine winner
$winner = array_keys($players, max($players))[0];

$username = $_SESSION['username'] ?? 'Player1';
$score = ''; //User score

//User wants to play again
if (isset($_POST['$play-again'])) {
    unset($_SESSION['players']);
    unset($_SESSION['board']);
    unset($_SESSION['current_player']);

    header("Location: lobby.php");
    exit();
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
        <h2 class="card-winner">Winner: <?php echo htmlentities($winner) ?> </h2>
        <p class="score-final">Final Score: <strong><?php echo $players[$winner]; ?></strong></p>

        <!-- Border to separate winner and player scores -->
        <hr class="border">

        <h3 class="player-scores">Player Results:</h3>

        <?php foreach ($players as $name => $score): ?>
            <p class="display-scores">
                <?php echo htmlentities($name); ?> - <strong><?php echo htmlentities($score); ?></strong>
            </p>
        <?php endforeach; ?>
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
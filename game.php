
<?php
session_start();
$username = $_SESSION['username'] ?? 'Player1';

//Default all questions to false (not answered)
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = [
        'Anime' => [100 => false, 200 => false, 300 => false, 400 => false],
        'Games' => [100 => false, 200 => false, 300 => false, 400 => false],
        'Science' => [100 => false, 200 => false, 300 => false, 400 => false],
        'History' => [100 => false, 200 => false, 300 => false, 400 => false],
        'Random' => [100 => false, 200 => false, 300 => false, 400 => false]
    ];
}

//Initialize players' score to 0
if (!isset($_SESSION['players'])) {
    $_SESSION['players'] = [
        'Player1' => 0,
        'Player2' => 0,
        'Player3' => 0,
        'Player4' => 0
    ];
}

//Track active player
if (!isset($_SESSION['turn'])) {
    $_SESSION['turn'] = 0; //Player1 starts first
}

$players = array_keys($_SESSION['players']);
$current_player = $players[$_SESSION['turn']];

//Update board when a question is answered
if (isset($_GET['category']) && isset($_GET['question'])) {
    $category = $_GET['category'];
    $questionValue = (int)$_GET['question'];

    //Mark as answered & update board if not already
    if ($_SESSION['board'][$category][$questionValue] === false) {
        $_SESSION['board'][$category][$questionValue] == true;
    }
    //Move to next player/player who answers correctly (latter part to be updated)
    $_SESSION['turn'] = ($_SESSION['turn'] + 1) % count($players);
}

//Track answered questions
function all_questions_answered() {
    foreach($_SESSION['board'] as $category => $questions) {
        foreach($questions as $question => $used) {
            if (!$used) { //If at least one question has not been used yet
                return false;
            }
        }
    }
    return true;
}

//Navigate to winner screen after all questions have been answered
if (all_questions_answered()) {
    header("Location: winner.php");
    exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy – Game Board</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page-wrapper game-page">
    <header class="header">
        <h1 class="title">JEOPARDY GAME SHOW</h1>
        <p class="subtitle">Battle Arena · Round 1</p>
        <p class="subtitle-small">Logged in as: <?php echo htmlspecialchars($username); ?></p>
    </header>

    <main class="game-layout">
        <!-- Jeopardy board -->
        <section class="board">
            <div class="board-row board-header">
                <div class="board-cell board-category">Anime</div>
                <div class="board-cell board-category">Games</div>
                <div class="board-cell board-category">Science</div>
                <div class="board-cell board-category">History</div>
                <div class="board-cell board-category">Random</div>
            </div>

            <!-- Replaced PHP code for used questions -->
             <?php for ($row = 100; $row <= 400; $row += 100): ?>
                <div class="board-row">
                    <?php foreach ($_SESSION['board'] as $category => $questions): ?>
                        <?php $used = $questions[$row]; ?>
                        <button class="board-cell board-tile <?php echo $used ? 'used' : ''; ?>" <?php echo $used ? 'disabled' : ''; ?>>
                            $<?php echo $row; ?>
                        </button>
                    <?php endforeach ?>
                </div>
             <?php endfor ?>
        </section>

        <!-- Player bar -->
        <section class="player-bar">
            <?php foreach ($_SESSION['players'] as $player => $score): ?>
                <div class="player-card <?php echo ($player === $username) ? 'player-active' : ''; ?>">
                    <div class="player-name"><?php echo htmlspecialchars($player); ?></div>
                    <div class="player-score"><?php echo $score; ?></div>
                </div>
            <?php endforeach ?>
        </section>
    </main>

    <footer class="footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
</div>
</body>
</html>

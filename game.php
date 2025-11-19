
<?php
session_start();
$username = $_SESSION['username'] ?? 'Player1';
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

            <!-- Row 1 -->
            <div class="board-row">
                <button class="board-cell board-tile">$100</button>
                <button class="board-cell board-tile">$100</button>
                <button class="board-cell board-tile">$100</button>
                <button class="board-cell board-tile">$100</button>
                <button class="board-cell board-tile">$100</button>
            </div>
            <!-- Row 2 -->
            <div class="board-row">
                <button class="board-cell board-tile">$200</button>
                <button class="board-cell board-tile">$200</button>
                <button class="board-cell board-tile">$200</button>
                <button class="board-cell board-tile">$200</button>
                <button class="board-cell board-tile">$200</button>
            </div>
            <!-- Row 3 -->
            <div class="board-row">
                <button class="board-cell board-tile">$300</button>
                <button class="board-cell board-tile">$300</button>
                <button class="board-cell board-tile">$300</button>
                <button class="board-cell board-tile">$300</button>
                <button class="board-cell board-tile">$300</button>
            </div>
            <!-- Row 4 -->
            <div class="board-row">
                <button class="board-cell board-tile">$400</button>
                <button class="board-cell board-tile">$400</button>
                <button class="board-cell board-tile">$400</button>
                <button class="board-cell board-tile">$400</button>
                <button class="board-cell board-tile">$400</button>
            </div>
        </section>

        <!-- Player bar -->
        <section class="player-bar">
            <div class="player-card player-active">
                <div class="player-name">Player 1</div>
                <div class="player-score">$1200</div>
            </div>
            <div class="player-card">
                <div class="player-name">Player 2</div>
                <div class="player-score">$800</div>
            </div>
            <div class="player-card">
                <div class="player-name">Player 3</div>
                <div class="player-score">$400</div>
            </div>
            <div class="player-card">
                <div class="player-name">Player 4</div>
                <div class="player-score">$0</div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
</div>
</body>
</html>

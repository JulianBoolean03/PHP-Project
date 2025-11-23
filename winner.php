<?php
session_start();
require_once __DIR__ . '/game_state.php';

// Handle theme toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_theme'])) {
  $current_theme = $_COOKIE['theme'] ?? 'dark';
  $new_theme = ($current_theme === 'dark') ? 'light' : 'dark';
  setcookie('theme', $new_theme, time() + 60 * 60 * 24 * 365, '/');
  $_COOKIE['theme'] = $new_theme;
}

// Get current theme
$theme = $_COOKIE['theme'] ?? 'dark';
$body_class = ($theme === 'light') ? 'light-theme' : '';

// Ensure user has a username
$username = $_SESSION['username'] ?? null;
if (!$username) {
  header('Location: profile.php');
  exit();
}

// Get players and scores (if game was never initialized, show placeholder)
$players = $_SESSION['players'] ?? [$username => 0];
$player_list = $_SESSION['player_list'] ?? [$username];

// Determine winner (highest score)
$max_score = max($players);
$winners = [];
foreach ($players as $player => $score) {
  if ($score === $max_score) {
    $winners[] = $player;
  }
}

// Handle tie
if (count($winners) > 1) {
  $winner_text = implode(' and ', $winners);
  $winner_message = "It's a tie!";
} else {
  $winner_text = $winners[0];
  $winner_message = "Winner: " . htmlspecialchars($winner_text);
}

// Handle Play Again button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['play_again'])) {
  // Reset game state but keep username
  reset_game();
  
  // Redirect to lobby
  header('Location: lobby.php');
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
<body class="<?php echo $body_class; ?>">

<!-- Confetti animation wrapper -->
<div class="confetti-wrapper">
    <?php for ($i = 0; $i < 150; $i++): ?>
        <div class="confetti"></div>
    <?php endfor; ?>
</div>

<div class="page-wrapper">
    <header class="header">
        <h1 class="title">JEOPARDY GAME SHOW</h1>
        <p class="subtitle">Game Over!</p>
        <p class="subtitle-small">Logged in as: <?php echo htmlspecialchars($username); ?></p>
    </header>

    <main class="card">
        <h2 class="card-winner"><?php echo $winner_message; ?></h2>
        
        <?php if (count($winners) === 1): ?>
            <p class="score-final">Final Score: <strong>$<?php echo $max_score; ?></strong></p>
        <?php else: ?>
            <p class="score-final">Tied Score: <strong>$<?php echo $max_score; ?></strong></p>
        <?php endif; ?>

        <!-- Border separator -->
        <hr class="border">

        <h3 class="player-scores">Player Results:</h3>

        <?php 
        // Sort players by score descending
        arsort($players);
        foreach ($players as $name => $score): 
        ?>
            <p class="display-scores">
                <?php echo htmlspecialchars($name); ?> - <strong>$<?php echo $score; ?></strong>
            </p>
        <?php endforeach; ?>

        <!-- Play Again button -->
        <form method="post" style="margin-top:1.5rem;">
           <button type="submit" name="play_again" class="btn-primary">
               Play Again
           </button>
        </form>
    </main>

    <footer class="footer">
      <form method="post" style="display:inline;">
        <button type="submit" name="toggle_theme" class="btn-theme">
          Switch to <?php echo ($theme === 'dark') ? 'Light' : 'Dark'; ?> Theme
        </button>
      </form>
      <p style="margin-top:0.5rem;">Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
</div>

</body>
</html>

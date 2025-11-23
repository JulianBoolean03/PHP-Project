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
  header('Location: profile.php?error=' . urlencode('Please create a username first.'));
  exit();
}

// Load shared game state from file
$gameStateFile = __DIR__ . '/shared_game_state.txt';
if (file_exists($gameStateFile)) {
  $sharedState = json_decode(file_get_contents($gameStateFile), true);
  if ($sharedState) {
    $_SESSION['board'] = $sharedState['board'];
    $_SESSION['players'] = $sharedState['players'];
    $_SESSION['player_list'] = $sharedState['player_list'];
    $_SESSION['current_player_index'] = $sharedState['current_player_index'];
  }
}

// Ensure game has been initialized
if (!isset($_SESSION['game_active']) || !$_SESSION['game_active']) {
  // Check if game is active via shared file
  $gameStatusFile = __DIR__ . '/game_status.txt';
  if (file_exists($gameStatusFile) && trim(file_get_contents($gameStatusFile)) === 'active') {
    // Game is active but this user's session doesn't know yet
    // Load the shared state
    if (file_exists($gameStateFile)) {
      $sharedState = json_decode(file_get_contents($gameStateFile), true);
      if ($sharedState) {
        $_SESSION['board'] = $sharedState['board'];
        $_SESSION['players'] = $sharedState['players'];
        $_SESSION['player_list'] = $sharedState['player_list'];
        $_SESSION['current_player_index'] = $sharedState['current_player_index'];
        $_SESSION['game_active'] = true;
        $_SESSION['question_bank'] = get_question_bank();
      } else {
        header('Location: lobby.php');
        exit();
      }
    } else {
      header('Location: lobby.php');
      exit();
    }
  } else {
    header('Location: lobby.php');
    exit();
  }
}

// Handle End Game button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['end_game'])) {
  header('Location: winner.php');
  exit();
}

// Check if all questions have been answered
if (all_questions_answered()) {
  header('Location: winner.php');
  exit();
}

// Get game state
$board = $_SESSION['board'] ?? [];
$players = $_SESSION['players'] ?? [];
$player_list = $_SESSION['player_list'] ?? [];
$current_player = get_current_player();

// Count total questions answered
$total_questions = 0;
$answered_questions = 0;
foreach ($board as $cat => $questions) {
  foreach ($questions as $val => $used) {
    $total_questions++;
    if ($used) $answered_questions++;
  }
}

$categories = array_keys($board);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Game Board</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Auto-refresh every 3 seconds to see updates from other players -->
    <meta http-equiv="refresh" content="3">
</head>
<body class="<?php echo $body_class; ?>">
<div class="page-wrapper game-page">
    <header class="header">
        <h1 class="title">JEOPARDY GAME SHOW</h1>
        <p class="subtitle">Battle Arena - Question <?php echo $answered_questions; ?> / <?php echo $total_questions; ?></p>
        <p class="subtitle-small">Logged in as: <?php echo htmlspecialchars($username); ?></p>
    </header>

    <main class="game-layout">
        <!-- Jeopardy board -->
        <section class="board board-fade-in">
            <!-- Category headers -->
            <div class="board-row board-header">
                <?php foreach ($categories as $cat): ?>
                    <div class="board-cell board-category"><?php echo htmlspecialchars($cat); ?></div>
                <?php endforeach; ?>
            </div>

            <!-- Question tiles -->
            <?php 
            $values = [100, 200, 300, 400];
            foreach ($values as $value): 
            ?>
                <div class="board-row">
                    <?php foreach ($categories as $cat): ?>
                        <?php $used = $board[$cat][$value] ?? false; ?>
                        <?php if ($used): ?>
                            <button class="board-cell board-tile used" disabled>
                                $<?php echo $value; ?>
                            </button>
                        <?php else: ?>
                            <form method="post" action="question.php" style="display:contents;">
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($cat); ?>">
                                <input type="hidden" name="value" value="<?php echo $value; ?>">
                                <button type="submit" class="board-cell board-tile">
                                    $<?php echo $value; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Current player indicator -->
        <section class="current-turn">
            <p><strong>Current Turn:</strong> <?php echo htmlspecialchars($current_player); ?></p>
        </section>

        <!-- Player bar -->
        <section class="player-bar">
            <?php foreach ($player_list as $player): ?>
                <div class="player-card <?php echo ($player === $current_player) ? 'player-active' : ''; ?>">
                    <div class="player-name"><?php echo htmlspecialchars($player); ?></div>
                    <div class="player-score">$<?php echo $players[$player] ?? 0; ?></div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- End game button -->
        <section style="text-align:center; margin-top:1rem;">
            <form method="post">
                <button type="submit" name="end_game" class="btn-secondary">
                    End Game Early
                </button>
            </form>
        </section>
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

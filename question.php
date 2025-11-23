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

// Ensure game is active
if (!isset($_SESSION['game_active']) || !$_SESSION['game_active']) {
  header('Location: lobby.php');
  exit();
}

$timer_duration = 30; // 30 seconds per question
$feedback_message = '';
$show_answer_form = true;

// Initialize question if POSTed from game.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category']) && isset($_POST['value'])) {
  $category = $_POST['category'];
  $value = (int)$_POST['value'];
  
  // Store current question in session
  $_SESSION['current_question'] = [
    'category' => $category,
    'value' => $value
  ];
  
  // Start timer
  $_SESSION['question_start'] = time();
}

// Check if we have a current question
if (!isset($_SESSION['current_question'])) {
  header('Location: game.php');
  exit();
}

$current_question = $_SESSION['current_question'];
$category = $current_question['category'];
$value = $current_question['value'];

// Get question data from bank
$question_bank = $_SESSION['question_bank'] ?? get_question_bank();
$question_data = $question_bank[$category][$value] ?? null;

if (!$question_data) {
  // Question not found, return to game
  unset($_SESSION['current_question']);
  unset($_SESSION['question_start']);
  header('Location: game.php');
  exit();
}

$question_text = $question_data['question'];
$correct_answer = $question_data['answer'];

// Calculate remaining time
$time_elapsed = time() - ($_SESSION['question_start'] ?? time());
$time_remaining = max(0, $timer_duration - $time_elapsed);

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
  $user_answer = trim($_POST['answer']);
  $current_player = get_current_player();
  
  if (check_answer($user_answer, $correct_answer)) {
    // Correct answer
    $_SESSION['players'][$current_player] += $value;
    $feedback_message = "Correct! +$" . $value;
  } else {
    // Incorrect answer
    $_SESSION['players'][$current_player] -= $value;
    $feedback_message = "Incorrect! The answer was: " . htmlspecialchars($correct_answer) . " (-$" . $value . ")";
  }
  
  // Mark question as used
  $_SESSION['board'][$category][$value] = true;
  
  // Advance to next player
  advance_turn();
  
  // Save shared state so all players see the update
  save_shared_game_state();
  
  // Clean up question state
  unset($_SESSION['current_question']);
  unset($_SESSION['question_start']);
  
  // Show feedback for 2 seconds using meta refresh
  $show_answer_form = false;
}

// Time expired - return to game
if ($time_remaining <= 0 && $show_answer_form) {
  // Mark question as used
  $_SESSION['board'][$category][$value] = true;
  
  // Advance turn
  advance_turn();
  
  // Save shared state
  save_shared_game_state();
  
  // Clean up
  unset($_SESSION['current_question']);
  unset($_SESSION['question_start']);
  
  header('Location: game.php');
  exit();
}

$current_player = get_current_player();
$players = $_SESSION['players'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Question</title>
    <link rel="stylesheet" href="styles.css">
    
    <?php if ($show_answer_form && $time_remaining > 0): ?>
    <!-- Auto-refresh when timer expires -->
    <meta http-equiv="refresh" content="<?php echo $time_remaining + 1; ?>">
    <?php elseif (!$show_answer_form): ?>
    <!-- Redirect back to game after showing feedback -->
    <meta http-equiv="refresh" content="3;url=game.php">
    <?php endif; ?>
    
    <!-- Animate timer bar based on remaining time -->
    <style>
        .timer-bar {
            animation-duration: <?php echo $time_remaining; ?>s;
        }
    </style>
</head>
<body class="<?php echo $body_class; ?>">
    <div class="page-wrapper">
        <header class="header">
            <h1 class="title">JEOPARDY GAME SHOW</h1>
            <p class="subtitle">Category: <?php echo htmlspecialchars($category); ?> - $<?php echo $value; ?></p>
            <p class="subtitle-small">Current Player: <?php echo htmlspecialchars($current_player); ?></p>
        </header>

        <main class="card">
            <h2 class="card-question"><?php echo htmlspecialchars($question_text); ?></h2>
            
            <?php if ($show_answer_form): ?>
                <!-- Timer bar -->
                <div class="timer-wrapper">
                    <div class="timer-bar"></div>
                </div>
                
                <p style="text-align:center; margin-bottom:1rem;">
                    Time Remaining: <strong><?php echo $time_remaining; ?>s</strong>
                </p>
                
                <!-- Answer form -->
                <form method="post" class="profile-form">
                    <label for="answer" class="label">Your Answer:</label>
                    <input 
                        type="text" 
                        id="answer" 
                        name="answer" 
                        class="input" 
                        required 
                        autofocus
                        placeholder="Type your answer here">
                    
                    <button type="submit" class="btn-primary">
                        Submit Answer
                    </button>
                </form>
            <?php else: ?>
                <!-- Feedback message after answer -->
                <div class="feedback-message <?php echo (strpos($feedback_message, 'Correct') !== false) ? 'feedback-correct' : 'feedback-incorrect'; ?>">
                    <p><?php echo $feedback_message; ?></p>
                </div>
                <p style="text-align:center; margin-top:1rem;">
                    Returning to game board...
                </p>
            <?php endif; ?>
            
            <!-- Display all player scores -->
            <div style="margin-top:1.5rem;">
                <h3 style="font-size:1rem; margin-bottom:0.5rem;">Current Scores:</h3>
                <?php foreach ($_SESSION['player_list'] as $player): ?>
                    <p style="font-size:0.9rem;">
                        <?php echo htmlspecialchars($player); ?>: 
                        <strong>$<?php echo $players[$player] ?? 0; ?></strong>
                    </p>
                <?php endforeach; ?>
            </div>
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

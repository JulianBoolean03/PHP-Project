<?php
session_start();

// Restore session from cookie if needed
if (!isset($_SESSION['username']) && !empty($_COOKIE['username'])) {
  $_SESSION['username'] = $_COOKIE['username'];
}

$current_user = $_SESSION['username'] ?? null;

// If we still have no user at all, send to profile with error
if (!$current_user) {
  header("Location: profile.php?error=" . urlencode("Please create a username first."));
  exit();
}

$lobbyFile = __DIR__ . '/lobby.txt';
$lobbyUsers = [];

// read lobby from file
if (file_exists($lobbyFile)) {
  $lobbyUsers = file($lobbyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// make sure this user is in the lobby list only once
if (!in_array($current_user, $lobbyUsers)) {
  $lobbyUsers[] = $current_user;
  file_put_contents($lobbyFile, implode(PHP_EOL, $lobbyUsers) . PHP_EOL, LOCK_EX);
}

$playerCount = count($lobbyUsers);
$errorMessage = '';

// handle "Start Game"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_game'])) {
  if ($playerCount < 2) {
    $errorMessage = "Waiting for more players… (Currently $playerCount / 2)";
  } else {
    $_SESSION['in_game'] = true;
    header('Location: game.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Jeopardy - Lobby</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <div class="page-wrapper">
    <header class="header">
      <h1 class="title">JEOPARDY GAME SHOW</h1>
      <p class="subtitle">Welcome <?php echo htmlspecialchars($current_user); ?>!</p>
    </header>

    <main class="card">
      <h2 class="card-title">Lobby</h2>
      <p class="card-text">
        Once at least two players have joined the lobby, you can start the game.
      </p>

      <?php if (!empty($errorMessage)): ?>
        <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
      <?php endif; ?>

      <h3 class="card-title" style="font-size:1.1rem; margin-top:0.8rem;">
        Players in Lobby (<?php echo $playerCount; ?>)
      </h3>
      <ul style="margin:0.5rem 0 1rem 1.2rem; font-size:0.95rem;">
        <?php foreach ($lobbyUsers as $name): ?>
          <li><?php echo htmlspecialchars($name); ?></li>
        <?php endforeach; ?>
      </ul>

      <form method="post">
        <button type="submit" name="start_game" class="btn-primary">
          Start Game
        </button>
      </form>
    </main>

    <footer class="footer">
      <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
  </div>
</body>

</html>

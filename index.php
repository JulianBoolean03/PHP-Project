<?php
session_start();

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

// AUTO-LOGIN from cookie: if we already know this user, go straight to lobby
if (!isset($_SESSION['username']) && !empty($_COOKIE['username'])) {
  $_SESSION['username'] = $_COOKIE['username'];
  header('Location: lobby.php');
  exit();
}

$currentUser = $_SESSION['username'] ?? ($_COOKIE['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Jeopardy – Home</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body class="<?php echo $body_class; ?>">
  <div class="page-wrapper">
    <header class="header">
      <h1 class="title">JEOPARDY GAME SHOW</h1>
      <p class="subtitle">Multiplayer Trivia Battle Arena</p>
      <?php if ($currentUser): ?>
        <p class="subtitle-small">
          You're currently signed in as
          <strong><?php echo htmlspecialchars($currentUser); ?></strong>.
        </p>
      <?php endif; ?>
    </header>

    <main class="card">
      <h2 class="card-title">Welcome!</h2>
      <p class="card-text">
        Create a profile, join the lobby, and battle your friends in a
        Jeopardy-style game board with categories and timed questions.
      </p>

      <div style="display:flex; flex-direction:column; gap:0.5rem;">
        <a href="profile.php"
          class="btn-primary"
          style="text-align:center; text-decoration:none; display:block;">
          Create / Change Username
        </a>

        <?php if ($currentUser): ?>
          <a href="lobby.php"
            class="btn-primary"
            style="text-align:center; text-decoration:none; display:block;">
            Rejoin Lobby
          </a>
        <?php endif; ?>
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

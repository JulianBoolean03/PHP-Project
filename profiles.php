<?php
session_start();

$errorMessage = '';
$usernameValue = '';

// file that stores all usernames (one per line)
$userFile = __DIR__ . '/usernames.txt';

// read existing usernames
$usernames = [];
if (file_exists($userFile)) {
  $usernames = file($userFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usernameValue = trim($_POST['username'] ?? '');

  if ($usernameValue === '') {
    $errorMessage = 'Please enter a username.';
  } elseif (in_array($usernameValue, $usernames)) {
    $errorMessage = 'Username already exists! Please enter a different one!';
  } else {
    // append username to file
    file_put_contents($userFile, $usernameValue . PHP_EOL, FILE_APPEND | LOCK_EX);

    // save to session and cookie
    $_SESSION['username'] = $usernameValue;
    setcookie('username', $usernameValue, time() + 60 * 60 * 24 * 30, '/'); // 30 days

    // redirect to lobby
    header('Location: lobby.php');
    exit();
  }
}

// if they already have a cookie, pre-fill for convenience
if ($usernameValue === '' && isset($_COOKIE['username'])) {
  $usernameValue = $_COOKIE['username'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Jeopardy – Create Account</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <div class="page-wrapper">
    <header class="header">
      <h1 class="title">Jeopardy Game Show</h1>
      <p class="subtitle">Create your account!</p>
    </header>

    <main class="card">
      <h2 class="card-title">Create Your Username</h2>
      <p class="card-text">
        Pick a unique username. This will be used to track your score
        during the game.
      </p>

      <?php if (!empty($errorMessage)): ?>
        <p class="error-message">
          <?php echo htmlspecialchars($errorMessage); ?>
        </p>
      <?php endif; ?>

      <form action="profile.php" method="post" class="profile-form">
        <label for="username" class="label">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          class="input"
          maxlength="20"
          required
          placeholder="e.g. Player1"
          value="<?php echo htmlspecialchars($usernameValue); ?>">

        <button type="submit" class="btn-primary">
          Create Account &amp; Join Lobby
        </button>
      </form>
    </main>

    <footer class="footer">
      <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
  </div>
</body>

</html>

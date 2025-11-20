<?php
session_start();

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

<body>
  <div class="page-wrapper">
    <header class="header">
      <h1 class="title">JEOPARDY GAME SHOW</h1>
      <p class="subtitle">Multiplayer Trivia Battle Arena</p>
      <?php if ($currentUser): ?>
        <p class="subtitle-small">
          You’re currently signed in as
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

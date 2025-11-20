<?php

session_start();

$timer = 30; //Set timer to 30 seconds. May be changed to be longer/shorter if needed

//Start timer when a question has been chosen & store timer once
if (!isset($_SESSION['question_start'])) {
    $_SESSION['question_start'] = time();
}

//Get remaining time
$time_elapse = time() - $_SESSION['question_start'];
$time_remain = max(0, $timer - $time_elapse);

//Automatically return to game board when timer ends
if ($time_remain <= 0) {
    unset($_SESSION['question_start']); //Resets timer for next question
    header("Location: game.php");
    exit();
}

//Question done
if (isset($_POST['exit_question'])) {
    unset($_SESSION['question_start']);
    header("Location: game.php");
    exit();
}

//Get question from array. To be filled later
$question = "Sample Question";
$username = $_SESSION['username'] ?? 'Player1';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Question</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Auto-refresh for updating PHP timer -->
    <meta http-equiv="refresh" content="<?php echo $time_remain ?>">

    <!-- Animation length for timer based on remaining time -->
     <style>
        .timer-bar {
            animation-duration: <?php echo $time_remain; ?>s;
        }
     </style>
</head>
<body>
    <div class = "page-wrapper">
        <header class="header">
            <h1 class="title">JEOPARDY GAME SHOW</h1>
            <p class="subtitle">Battle Arena · Question</p>
            <p class="subtitle-small">Logged in as <?php echo htmlspecialchars($username); ?></p>
        </header>

    <main class="card">
        <h2 class="card-title" style:"text-align: center;"><?php echo htmlspecialchars($question) ?></h2>

        <!-- Timer -->
         <div class = "timer-wrapper">
            <div class = "timer-bar"></div>
         </div>
    </main>

    <!-- Button for when a user answers correctly/no one gets it correct -->
    <form method="post">
        <button class="btn-primary" name="exit_question">Exit</button>
    </form>

    <footer class = "footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
    </div>
</body>
</html>
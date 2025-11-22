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
if ($time_remain == 0) {
    unset($_SESSION['question_start']); //Resets timer for next question
    header("Location: game.php?timeout=1");
    exit();
}

//Question done
if (isset($_POST['exit_question'])) {
    unset($_SESSION['question_start']);
    header("Location: game.php?manual_exit=1");
    exit();
}

//Get question from array. To be filled later
$question = "Sample Question";
//Display correct answer (will be updated later)
$correct_answer = "Sample Answer";
$user_answer = isset($_POST['answer']) ? trim($_POST['answer']) : '';

//Check if user answered correctly
$is_correct = strtolower($user_answer) === strtolower($correct_answer);
//Check if user answered the question
$is_answered = !empty($user_answer);
$username = $_SESSION['username'] ?? 'Player1';

//If user answers correctly, update score
if ($is_answered) {
    $questionValue = (int)$_GET['question'];
    if ($is_correct) {
        $_SESSION['players'][$username] += $questionValue;
    } else {
        $_SESSION['players'][$username] -= $questionValue;
    }

    //Mark question as answered in game board
    $category = $_GET['category'];
    $_SESSION['board'][$category][$questionValue] = true;
    header("Location: game.php");
    exit();
}
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
        <h2 class="card-question"><?php echo htmlspecialchars($question) ?></h2>
      
        <!-- Timer -->
        <div class = "timer-wrapper">
            <div class = "timer-bar"></div>
        </div>
    </main>

    <!-- Form for submitting answer and navigates back to game.php while adding/subtracting score depending on answer -->
    <form method="post">
        <input type="text" name="answer" placeholder="Answer">
        <button class="btn-primary" name="exit_question">Submit Answer</button>
    </form>

    <footer class = "footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
    </div>
</body>
</html>
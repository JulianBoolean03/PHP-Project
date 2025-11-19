<?php

$timer = 30; //Set timer to 30 seconds. May be changed to be longer/shorter if needed

//Start timer when question shows up on screen
session_start();
if (!isset($_SESSION['question_start'])) {
    $_SESSION['question_start'] = time();
}

//Get remaining time
$time_elapse = time() - $_SESSION['question_start'];
$time_remain = max(0, $timer - $time_elapse);

//Automatically return to game board when timer ends
if ($time_remain <= 0) {
    unset($_SESSION['question_start']); //Resets timer for next question
    header("Location: board.php"); //replace with name of game page if needed
    exit();
} //Put else for when time_remain is greater than 0

//Get question from array
$question = "Sample Question";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy - Question</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Auto-refresh for PHP time-checking -->
     <meta http-equiv="refresh" content="1">
</head>
<body>
    <div class = "page-wrapper">
        <header class = "header">
            <h1 class = "title">Jeopardy Game Show</h1>
            <p class = "subtitle" >Game Time!</p>
        </header>

    <main class = "card">
        <h2 class= "card-title">Question:</h2>
        <!-- Section for question since there will be different questions leaving this blank for now -->

        <!-- Timer -->
         <div class = "timer-wrapper">
            <div class = "timer-bar"></div>
         </div>

         <p><?php echo htmlspecialchars($question); ?></p>
    </main>

    <footer class = "footer">
        <p>Julian Robinson &amp; Amanda Nguyen</p>
    </footer>
    </div>
</body>
</html>
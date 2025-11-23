<?php
/* game_state.php
 * Centralized game state management and question bank
 * This file contains all questions, initialization logic, and helper functions
 */

// Question bank: [category][value] => ['question' => ..., 'answer' => ...]
function get_question_bank() {
    return [
        'Anime' => [
            100 => ['question' => 'This anime features a ninja named Naruto from which village?', 'answer' => 'Hidden Leaf'],
            200 => ['question' => 'In Death Note, what is the name of the Shinigami who drops the Death Note?', 'answer' => 'Ryuk'],
            300 => ['question' => 'In Attack on Titan, what are the three walls called?', 'answer' => 'Maria, Rose, Sina'],
            400 => ['question' => 'What is the name of the organization in Fullmetal Alchemist that the protagonists work for?', 'answer' => 'State Military']
        ],
        'Games' => [
            100 => ['question' => 'What is the best-selling video game of all time?', 'answer' => 'Minecraft'],
            200 => ['question' => 'In The Legend of Zelda, what is the name of the main protagonist?', 'answer' => 'Link'],
            300 => ['question' => 'What gaming company created the Pokemon franchise?', 'answer' => 'Nintendo'],
            400 => ['question' => 'In what year was the first Super Mario Bros game released?', 'answer' => '1985']
        ],
        'Science' => [
            100 => ['question' => 'What is the chemical symbol for water?', 'answer' => 'H2O'],
            200 => ['question' => 'How many bones are in the adult human body?', 'answer' => '206'],
            300 => ['question' => 'What is the speed of light in a vacuum (in meters per second)?', 'answer' => '299792458'],
            400 => ['question' => 'What is the name of the process by which plants make their food?', 'answer' => 'Photosynthesis']
        ],
        'History' => [
            100 => ['question' => 'Who was the first President of the United States?', 'answer' => 'George Washington'],
            200 => ['question' => 'In what year did World War II end?', 'answer' => '1945'],
            300 => ['question' => 'What ancient wonder was located in Alexandria, Egypt?', 'answer' => 'Lighthouse'],
            400 => ['question' => 'Who was the first person to walk on the moon?', 'answer' => 'Neil Armstrong']
        ],
        'Random' => [
            100 => ['question' => 'What is the capital of France?', 'answer' => 'Paris'],
            200 => ['question' => 'How many continents are there on Earth?', 'answer' => '7'],
            300 => ['question' => 'What is the largest ocean on Earth?', 'answer' => 'Pacific'],
            400 => ['question' => 'What is the smallest country in the world by area?', 'answer' => 'Vatican City']
        ]
    ];
}

// Initialize a new game with players from lobby
function init_game($players) {
    $categories = ['Anime', 'Games', 'Science', 'History', 'Random'];
    $values = [100, 200, 300, 400];
    
    // Initialize board state - all questions available
    $board = [];
    foreach ($categories as $cat) {
        $board[$cat] = [];
        foreach ($values as $val) {
            $board[$cat][$val] = false; // false = not used yet
        }
    }
    
    // Initialize player scores
    $player_scores = [];
    foreach ($players as $player) {
        $player_scores[$player] = 0;
    }
    
    // Store in session
    $_SESSION['board'] = $board;
    $_SESSION['players'] = $player_scores;
    $_SESSION['player_list'] = array_values($players); // indexed array
    $_SESSION['current_player_index'] = 0;
    $_SESSION['game_active'] = true;
    $_SESSION['question_bank'] = get_question_bank();
    
    // Save to shared file for all players
    save_shared_game_state();
}

// Save game state to shared file
function save_shared_game_state() {
    $gameStateFile = __DIR__ . '/shared_game_state.txt';
    $state = [
        'board' => $_SESSION['board'] ?? [],
        'players' => $_SESSION['players'] ?? [],
        'player_list' => $_SESSION['player_list'] ?? [],
        'current_player_index' => $_SESSION['current_player_index'] ?? 0
    ];
    file_put_contents($gameStateFile, json_encode($state), LOCK_EX);
}

// Reset game state for a new game (keeps usernames)
function reset_game() {
    unset($_SESSION['board']);
    unset($_SESSION['players']);
    unset($_SESSION['player_list']);
    unset($_SESSION['current_player_index']);
    unset($_SESSION['game_active']);
    unset($_SESSION['question_bank']);
    unset($_SESSION['current_question']);
    unset($_SESSION['question_start']);
    
    // Reset shared files
    $gameStatusFile = __DIR__ . '/game_status.txt';
    $gameStateFile = __DIR__ . '/shared_game_state.txt';
    $lobbyFile = __DIR__ . '/lobby.txt';
    
    file_put_contents($gameStatusFile, 'waiting', LOCK_EX);
    if (file_exists($gameStateFile)) {
        unlink($gameStateFile);
    }
    // Don't clear lobby file here - let users rejoin
}

// Check if all questions have been answered
function all_questions_answered() {
    if (!isset($_SESSION['board'])) return false;
    
    foreach ($_SESSION['board'] as $category => $questions) {
        foreach ($questions as $value => $used) {
            if (!$used) {
                return false; // Found an unused question
            }
        }
    }
    return true;
}

// Get current player username
function get_current_player() {
    if (!isset($_SESSION['player_list']) || !isset($_SESSION['current_player_index'])) {
        return null;
    }
    $index = $_SESSION['current_player_index'];
    $players = $_SESSION['player_list'];
    return $players[$index] ?? null;
}

// Advance to next player
function advance_turn() {
    if (!isset($_SESSION['player_list']) || !isset($_SESSION['current_player_index'])) {
        return;
    }
    $playerCount = count($_SESSION['player_list']);
    $_SESSION['current_player_index'] = ($_SESSION['current_player_index'] + 1) % $playerCount;
}

// Check if answer is correct (case-insensitive, trimmed)
function check_answer($user_answer, $correct_answer) {
    $user = trim(strtolower($user_answer));
    $correct = trim(strtolower($correct_answer));
    
    // Exact match
    if ($user === $correct) return true;
    
    // Check if user answer contains the correct answer
    if (strpos($user, $correct) !== false) return true;
    
    return false;
}

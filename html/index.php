<?php
$basePath = realpath(__DIR__ . '/..');
require_once("$basePath/classes/IUGO/autoload.php");

require_once("$basePath/classes/App/DAO.php");

\IUGO\ClassLoader::register('\\App', "$basePath/classes/App");

// Add endpoints here
$processors = [
    'timestamp' => new \App\TimeProcessor(), 

    'transaction' => new \App\Transaction(),
    'transactionstats' => new \App\Transaction(),

    'scorepost' => new \App\LeaderBoard,
    'leaderboardget' => new \App\LeaderBoard,

    'usersave' => new \App\User,
    'userload' => new \App\User,

    'databaseformat' => new \App\DatabaseFormat,
];

$request = new \IUGO\HttpRequest($_GET, $_POST, $_SERVER, file_get_contents('php://input'));

$engine = new \IUGO\Engine($processors);
$engine->processRequest($request);

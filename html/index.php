<?php
$basePath = realpath(__DIR__ . '/..');
require_once("$basePath/classes/IUGO/autoload.php");

\IUGO\ClassLoader::register('\\App', "$basePath/classes/App");

// Add endpoints here
$processors = [
];

$request = new \IUGO\HttpRequest($_GET, $_POST, $_SERVER, file_get_contents('php://input'));

$engine = new \IUGO\Engine($processors);
$engine->processRequest($request);

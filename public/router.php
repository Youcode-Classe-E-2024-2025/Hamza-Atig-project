<?php
define('BASE_PATH', __DIR__ . '/../');
define('BASE_URL', '/');
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$routes = [
    '' => 'index.php',
    'index.php' => 'index.php',
    'login.php' => 'login.php',
    'logout.php' => 'logout.php',
    'signup.php' => 'signup.php',
    'inbox.php' => 'inbox.php',
    'work.php' => 'work.php',
    'addtask.php' => 'addtask.php',
    'createpro.php' => 'createpro.php',
    'geust.php' => 'geust.php',
    'member.php' => 'member.php',
    'send_request.php' => 'send_request.php',
    'export.php' => 'export.php'
];

if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
} else {
    http_response_code(404);
    require '404.php';
}

<?php

require_once __DIR__ . '/../vendor/autoload.php';
umask(0000);

define('STDERR', fopen('php://stderr', 'a+'));

$app = new \PhpDoxinizer\Application(array('debug' => true, 'config' => $_ENV));
$app->run();

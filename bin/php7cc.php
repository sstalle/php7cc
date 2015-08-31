<?php

$autoloadFiles = array(
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
);

$loader = null;
foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        $loader = require $autoloadFile;
        break;
    }
}

if (!$loader) {
    exit('Autoloader not found. Try installing dependencies using composer install.');
}

$app = new \Sstalle\php7cc\Infrastructure\Application();
$app->run();

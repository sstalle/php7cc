<?php

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

$app = new \Sstalle\php7cc\Infrastructure\Application();
$app->run();
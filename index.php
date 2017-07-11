<?php

require_once __DIR__ . '/vendor/autoload.php';

use Iubar\Core\Application;

$app = new Application();

require_once __DIR__ . '/src/routes.php';

$app->run();
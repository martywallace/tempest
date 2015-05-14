<?php

define('ROOT', __DIR__ . '/../');

require(ROOT . '/vendor/autoload.php');

$app = new \Tempest\Tempest();
$app->start();
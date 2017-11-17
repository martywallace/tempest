<?php

$cwd = getcwd();
$ven = realpath(__DIR__ . '/../');

// Structure for "app".
mkdir($cwd . '/app');
mkdir($cwd . '/app/storage');
mkdir($cwd . '/app/src');
mkdir($cwd . '/app/templates');
mkdir($cwd . '/app/kernel');

file_put_contents($cwd . '/app/storage/.gitkeep', '');
file_put_contents($cwd . '/app/templates/.gitkeep', '');

copy($ven . '/bin/templates/App.php', $cwd . '/app/src/App.php');
copy($ven . '/bin/templates/http.php', $cwd . '/app/kernel/http.php');
copy($ven . '/bin/templates/config.php', $cwd . '/app/config.php');

// Structure for "public".
mkdir($cwd . '/public');

copy($ven . '/bin/templates/index.php', $cwd . '/public/index.php');

// Root.
file_put_contents($cwd . '/.gitignore', implode(PHP_EOL, ['.DS_Store', '.idea', '.env', 'vendor']));
file_put_contents($cwd . '/.env', 'DEV=true');
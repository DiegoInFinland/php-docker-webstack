<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' =>  false,
]);
$title = 'Home';

$path = __DIR__ . '/../sites';
$show_dir = preg_grep('/^([^.])/', scandir($path));

// need to be fixed. Twig can't find $path 
$twig->addFunction(new \Twig\TwigFunction('is_dir', function ($path) {
    return is_dir($path);
}));

echo $twig->render('home.twig', [
    'show_dir' => $show_dir,
    'title' => $title,
    'sites_path' => 'sites'
]);

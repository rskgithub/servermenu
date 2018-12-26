<?php
require '../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors','1');

// Prepare app
$app = new \Slim\Slim(array(
	'templates.path' => __DIR__.'/../templates',
	'cookies.encrypt' => true,
));

// Start session
//session_cache_limiter(false);
session_start();

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
	'charset' => 'utf-8',
	'cache' => realpath('../cache'),
	'auto_reload' => true,
	'strict_variables' => false,
	'autoescape' => true,
);

$app->view->parserExtensions = array(
	new \Slim\Views\TwigExtension(),
	new \JSW\Twig\TwigExtension()
);

$twig = $app->view->getEnvironment();
$twig->addGlobal('session', $_SESSION);
$twig->addFunction('title', new Twig_Function_Function('ServerMenu\Utility::emphasizeHd'));


// Add configuration to application
$configFile = '../app/config.php';
if (!file_exists($configFile)) die("Config file \"$configFile\" not found");
$config = include($configFile);
$app->config('s', $config);
$app->view->setData('config', $app->config('s'));

// Include routes
include('../app/routes.php');

// Run app
$app->run();

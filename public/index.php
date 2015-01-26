<?php
require '../vendor/autoload.php';

error_reporting(E_ALL);
ini_set( 'display_errors','1');

// Prepare app
$app = new \Slim\Slim(array(
	'templates.path' => '../templates',
	'cookies.encrypt' => true,
));

// Start session
session_cache_limiter(false);
session_start();

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
	'charset' => 'utf-8',
	'cache' => realpath('../templates/cache'),
	'auto_reload' => true,
	'strict_variables' => false,
	'autoescape' => true
);

$app->view->parserExtensions = array(
	new \Slim\Views\TwigExtension(),
	new \JSW\Twig\TwigExtension()
);


// Add configuration to application
$configFile = '../app/config.php';
if (!file_exists($configFile)) die("Config file \"$configFile\" not found");
$config = include($configFile);
$app->config('s', $config);

/*
 * Authentication
 */

$app->hook('slim.before', function() use ($app) {
	if (!isset($_SESSION['login'])
		&& $app->request->getResourceUri() != '/login')
		$app->redirect('/login');
});

/*
 * Views/Pages
 */

$app->get('/', '\ServerMenu\Controllers\Application:getIndex');
$app->get('/login', '\ServerMenu\Controllers\Application:getLogin');
$app->post('/login', '\ServerMenu\Controllers\Application:postLogin');


/*
 * JSON API Actions
 */

// API middleware
function apiRequest() {
	$app = \Slim\Slim::getInstance();
	$app->view(new \JsonApiView());
	$app->add(new \JsonApiMiddleware());
}

$app->get('/api/:type', 'apiRequest', '\ServerMenu\Controllers\Api:getListPlugins');
$app->get('/api/receivers/:pluginType/:receiverType', 'apiRequest', '\ServerMenu\Controllers\Api:getListReceivers');
$app->post('/api/send/:pluginType/:pluginId', 'apiRequest', '\ServerMenu\Controllers\Api:postSend');
$app->get('/api/search/:pluginId/:amount/:beginAt/:searchQuery', 'apiRequest', '\ServerMenu\Controllers\Api:getSearch');

/*
 * AJAX Actions
 */

// Get Specific Service
$app->get('/ajax/:serviceType/:serviceId', '\ServerMenu\Controllers\Ajax:getService');
$app->get('/ajax/search/:pluginId/:query', '\ServerMenu\Controllers\Ajax:getSearch');


// Define 404 template
$app->notFound(function () use ($app) {
	$app->render('404.html.twig');
});

// Run app
$app->run();
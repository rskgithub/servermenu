<?php
	
	
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

$app->get('/login', '\ServerMenu\Controllers\Application:getLogin');
$app->get('/logout', '\ServerMenu\Controllers\Application:getLogout');
$app->post('/login', '\ServerMenu\Controllers\Application:postLogin');

$app->get('/', '\ServerMenu\Controllers\Application:getIndex');
$app->get('/config', '\ServerMenu\Controllers\Config:getIndex');


/*
 * JSON API Actions
 */

// API middleware
function apiRequest() {
	$app = \Slim\Slim::getInstance();
	$app->view(new \JsonApiView());
	$app->add(new \JsonApiMiddleware());
}

$app->get('/api/file-list', 'apiRequest', '\ServerMenu\Controllers\Api:getFileList');	
$app->get('/api/:type', 'apiRequest', '\ServerMenu\Controllers\Api:getListPlugins');
$app->get('/api/receivers/:pluginType/:receiverType', 'apiRequest', '\ServerMenu\Controllers\Api:getListReceivers');
$app->post('/api/send/:pluginType/:pluginId', 'apiRequest', '\ServerMenu\Controllers\Api:postSend');
$app->get('/api/search/:pluginId/:amount/:beginAt/:searchQuery', 'apiRequest', '\ServerMenu\Controllers\Api:getSearch');

/*
 * AJAX Actions
 */

// Get Specific Service
$app->get('/ajax/app/:id', '\ServerMenu\Controllers\Ajax:get');
$app->get('/ajax/:serviceType/:serviceId', '\ServerMenu\Controllers\Ajax:getService');
$app->get('/ajax/search/:pluginId/:query', '\ServerMenu\Controllers\Ajax:getSearch');

// Define 404 template
$app->notFound(function () use ($app) {
	$app->render('404.html.twig');
});

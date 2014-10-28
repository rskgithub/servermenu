<?php
require '../vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
	'templates.path' => '../templates',
));

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
$config = include('../app/config.php');
$app->config('s', $config);

/*
 * Views/Pages
 */

// Front page
$app->get('/', function () use ($app, $config) {
	$template_variables = array(
		'config' => $config
	);

	$app->render('index.html.twig', $template_variables);
});


/*
 * JSON API Actions
 */

// API middleware
function apiRequest()
{
	$app = \Slim\Slim::getInstance();
	$app->view(new \JsonApiView());
	$app->add(new \JsonApiMiddleware());
}

// Get list of plugins
$app->get('/api/:type', 'apiRequest', function ($type) use ($app, $config) {
	if (empty($config[$type])) {
		$app->notFound();
		return;
	}

	$app->render(200, array(
		$type => array_keys($config[$type]),
	));
});

$app->get('/api/receivers/:pluginType/:receiverType', 'apiRequest',
	function ($pluginType, $receiverType) use ($app, $config) {
		$plugins = \ServerMenu\PluginLoader::getReceivers($pluginType, $receiverType);
		$app->render(200, $plugins);
	}
);

$app->post('/api/send/:pluginType/:pluginId', 'apiRequest',
	function ($pluginType, $pluginId) use ($app, $config) {
		if (!$plugin = \ServerMenu\PluginLoader::fetch($pluginType, $pluginId))
			return $app->notFound();

		$result = $plugin->receive($_POST['receivertype'], $_POST['content']);

		$app->render(200, array('result' => $result));
	}
);

$app->get('/api/search/:pluginId/:amount/:beginAt/:searchQuery', 'apiRequest',
	function ($pluginId, $amount, $beginAt, $searchQuery) use ($app, $config) {
		/* @var $plugin \ServerMenu\SearchEngine */
		if (!$plugin = \ServerMenu\PluginLoader::fetch('searchEngine', $pluginId))
			return $app->notFound();

		$result = $plugin->getTemplateData($searchQuery, $amount, $beginAt);

		$app->render(200, array('result' => $result));
	}
);

/*
 * AJAX Actions
 */

// Get Specific Service
$app->get('/ajax/:serviceType/:serviceId', function ($serviceType, $serviceId) use ($app, $config) {
	//sleep(rand(1, 2));

	if (!isset($config[$serviceType . 's'][$serviceId])) {
		$app->notFound();
		return;
	}

	$service = \ServerMenu\PluginLoader::fetch($serviceType, $serviceId);

	// Render Service HTML
	$app->render($serviceType . '.html.twig', $service->getTemplateData(), 200);
});


// Define 404 template
$app->notFound(function () use ($app) {
	$app->render('404.html.twig');
});

// Run app
$app->run();
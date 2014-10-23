<?php
require '../vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
        'templates.path' => '../templates',
));

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
        'charset'          => 'utf-8',
        'cache'            => realpath('../templates/cache'),
        'auto_reload'      => true,
        'strict_variables' => false,
        'autoescape'       => true
);

$app->view->parserExtensions = array(
        new \Slim\Views\TwigExtension(),
        new \JSW\Twig\TwigExtension()
);

// Define 404 template
$app->notFound(function () use ($app) {
        $app->render('404.html.twig');
});

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

// Get list of services
$app->get('/api/services', 'apiRequest', function () use ($app, $config) {
        if (empty($config['services'])) {
                $app->notFound();
        }

        $app->render(200, array(
                'services' => array_keys($config['services']),
        ));
});


/*
 * AJAX Actions
 */

// Get Specific Service
$app->get('/ajax/service/:serviceId', function ($serviceId) use ($app, $config) {
        if (!isset($config['services'][$serviceId])) {
                $app->notFound();
        }

        $serviceConfig = $config['services'][$serviceId];
        $serviceClass = "\\ServerMenu\\Services\\{$serviceConfig['service']}";
        /* @var $service \ServerMenu\Service */
        $service = new $serviceClass($serviceConfig, $serviceId);
        // Render Service HTML
        $app->render('service.html.twig', $service->getTemplateData());
});



// Run app
$app->run();
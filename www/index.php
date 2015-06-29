<?php
/*
 * This file is part of the xpdo.org package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$startTime = microtime(true);

require __DIR__ . '/../vendor/autoload.php';

$config = [
    "debug" => false,
    "https.port"=> 443,
    "session.name" => 'xpdo.org',
    "session.lifetime" => 0,
    "templates.path" => __DIR__ . '/../views',
    "db" => [
        \xPDO\xPDO::OPT_HYDRATE_FIELDS => true,
        \xPDO\xPDO::OPT_HYDRATE_RELATED_OBJECTS => true,
        \xPDO\xPDO::OPT_HYDRATE_ADHOC_FIELDS => true,
        \xPDO\xPDO::OPT_CONNECTIONS => [
            [
                'dsn' => 'sqlite:' . __DIR__ . '/../data/xpdo',
                'username' => '',
                'password' => '',
                'options' => [
                    \xPDO\xPDO::OPT_CONN_MUTABLE => true,
                ],
                'driverOptions' => [],
            ],
        ],
    ]
];

if (is_readable(__DIR__ . '/../config.php')) {
    $loaded = include __DIR__ . '/../config.php';
    if (is_array($loaded)) {
        $config = array_merge($config, $loaded);
    }
}

$app = new \Slim\Slim($config);

/** @var \Slim\Views\Twig $view */
$view = $app->view(new \Slim\Views\Twig());
$view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath(implode('/', [$app->config('templates.path'), 'cache'])),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$view->parserExtensions = [
    new \Slim\Views\TwigExtension(),
    new \Tacit\Views\TwigExtension()
];

$app->error(function(\Exception $e) use ($app) {
    $data = [
        'error' => true,
        'errorCode' => $e instanceof \Tacit\Client\RestfulException ? $e->getStatusCode() : 500,
        'errorMessage' => $e->getMessage(),
        'errorDescription' => $e instanceof \Tacit\Client\RestfulException ? $e->getDescription() : null,
        'errors' => $e instanceof \Tacit\Client\RestfulException ? (isset($e->getResource()['property']) ? $e->getResource()['property'] : null) : null
    ];
    $app->getLog()->error($e->getMessage(), $data);
    $app->render('500.twig', $data);
    $app->stop();
});

$app->container->singleton('identities', function() use ($app) {
    return (new \Tacit\Client\Identity($app->config('api.identities')));
});
$app->container->singleton('api', function() use ($app) {
    return new \Tacit\Client($app, $app->config('api.endpoint'));
});
$app->container->singleton('db', function() use ($app) {
    return \xPDO\xPDO::getInstance('db', $app->config('db'));
});

session_name($app->config('session.name'));
session_set_cookie_params(
    (integer)$app->config('session.lifetime'),
    rtrim($app->request->getRootUri(), '/') . '/'
);

$app->add(new \Tacit\Middleware\Session());

require __DIR__ . '/routes.php';

$app->run();

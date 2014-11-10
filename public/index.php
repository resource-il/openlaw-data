<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

define('PUBLIC_DIR', __DIR__);
define('BASE_DIR', realpath(PUBLIC_DIR . '/../'));
define('APP_DIR', BASE_DIR . '/app');

define('OPENLAW_VERSION', '0.1.2');

require_once BASE_DIR . '/vendor/autoload.php';

$app = new Openlaw\Silex\Application();
$app['debug'] = true;

$app->get(
  '/',
  function () {
      return 'Hello, This is the OpenLaw data-set!';
  }
);

$app->error(
  function (\Exception $e, $code) use ($app) {
      return (new \Openlaw\Controller($app))->errorHandler($e, $code);
  }
);

$app->after(
  function (Request $request, Response $response) {
      if ($response instanceof JsonResponse && $callback = $request->query->get('callback', false)) {
          $response->setCallback($callback);
      }
  }
);

// ... definitions

$app->run();

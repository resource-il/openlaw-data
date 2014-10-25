<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function() {
      return 'Hello, This is the OpenLaw data-set!';
  });

$app->error(function(\Exception $e, $code) use ($app) {
      return (new \Openlaw\Controller())->errorHandler($e, $code);
  });

// Booklets
$app->get('/booklet/','Openlaw\\Controller\\Booklet::index');

$app->get('/booklet/{booklet}/','Openlaw\\Controller\\Booklet::single')
  ->convert('booklet', 'Openlaw\\Data\\Booklet::factory')
  ->assert('booklet', '\d+');

$app->get('/booklet/{booklet}/part/','Openlaw\\Controller\\Booklet::singleWithParts')
  ->convert('booklet', 'Openlaw\\Data\\Booklet::factory')
  ->assert('booklet', '\d+');

$app->get('/booklet/year/{year}/','Openlaw\\Controller\\Booklet::byYear')
  ->convert('year', 'Openlaw\\Data\\Booklet::factoryYear')
  ->assert('year', '\d+');

$app->get('/booklet/knesset/{knesset}/','Openlaw\\Controller\\Booklet::byKnesset')
  ->assert('knesset', '\d+')
  ->convert('knesset', 'Openlaw\\Data\\Booklet::factoryKnesset');

// Parts
$app->get('/booklet/{booklet}/part/{part}/','Openlaw\\Controller\\Part::single')
  ->assert('booklet', '\d+')
  ->assert('part', '\d+');

// ... definitions

$app->run();

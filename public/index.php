<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function() {
      return 'Hello, Hasadna!';
  });

$app->get('/booklet','Openlaw\\Controller\\Booklet::index');

$app->get('/booklet/{booklet}','Openlaw\\Controller\\Booklet::single')
  ->convert('booklet', 'Openlaw\\Data\\Booklet::factory');

$app->get('/booklet/{booklet}/part','Openlaw\\Controller\\Booklet::singleWithParts')
  ->convert('booklet', 'Openlaw\\Data\\Booklet::factory');

$app->get('/booklet/{booklet}/part/{part}','Openlaw\\Controller\\Part::single');

// ... definitions

$app->run();

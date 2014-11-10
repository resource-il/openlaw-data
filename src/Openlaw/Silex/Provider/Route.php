<?php

namespace Openlaw\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class Route implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public static function mountProvider(Application $app)
    {
        $app->register(new static());
    }

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['route'] = $app->share($app->extend('routes', function($routes, $app) {
                $loader = new YamlFileLoader(new FileLocator(BASE_DIR . '/config'));
                $collection = $loader->load('routing.yml');

                /** @var RouteCollection $routes */
                $routes->addCollection($collection);

                return $routes;
            }));
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {

    }
}

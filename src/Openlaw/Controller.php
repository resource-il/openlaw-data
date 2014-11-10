<?php

namespace Openlaw;

use Openlaw\Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected $app = null;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function errorHandler(\Exception $e, $code)
    {
        return $this->app->json(
          [],
          [
            'code' => $code,
            'message' => $e->getMessage(),
          ]
        );
    }
}

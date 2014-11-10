<?php

namespace Openlaw\Silex;

use Openlaw\Silex\Provider\Route;
use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\JsonResponse;

class Application extends SilexApplication
{
    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        Route::mountProvider($this);
    }

    /**
     * Convert some data into a JSON response.
     *
     * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     *
     * @return JsonResponse
     */
    public function json($data = [], $error = [], $usage = [], $status = 200, array $headers = [])
    {
        $data = [
          'response' => $data,
          'usage' => $usage,
          'error' => $error,
          'license' => 'CC-BY',
          'copyright' => [
            'year' => '2014',
            'name' => 'The Public Knowledge Workshop',
            'url' => 'http://www.hasadna.org.il/',
          ],
          'version' => OPENLAW_VERSION,
        ];
        $headers['Access-Control-Allow-Origin'] = '*';
        return parent::json($data, $status, $headers);
    }
}


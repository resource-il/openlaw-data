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

    protected function response($data = '', $headers = [], $code = Response::HTTP_OK)
    {
        return new Response($data, $code, $headers);
    }

    protected function json($data = [], $error = [], $usage = [])
    {
        $response = [
          'content' => $data,
          'usage' => $usage,
          'error' => $error,
          'license' => 'CC-BY',
          'copyright' => [
            'year' => '2014',
            'name' => 'The Public Knowledge Workshop',
            'url' => 'http://www.hasadna.org.il/',
          ],
        ];

        $jsonEncodeOptions = 0;
        if (!empty($error)) {
            $jsonEncodeOptions += JSON_FORCE_OBJECT;
        }

        $json = json_encode($response, $jsonEncodeOptions);

        if (isset($_GET['callback'])) {
            $json = $_GET['callback'] . '(' . $json . ');';
        }

        return $this->response($json, ['Content-Type' => 'application/json']);
    }

    protected function error($data = [])
    {

        return $this->json([], $data);
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

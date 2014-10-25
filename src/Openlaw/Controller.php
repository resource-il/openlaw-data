<?php

namespace Openlaw;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
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
          'license' => 'CC-BY-SA',
          'copyright' => [
            'year' => '2014',
            'name' => 'OpenLaw a project of The Public Knowledge Workshop (R.O.)',
            'url' => 'http://www.openlaw.org.il/',
          ],
        ];

        $jsonEncodeOptions = 0;
        if (!empty($error)) {
            $jsonEncodeOptions += JSON_FORCE_OBJECT;
        }

        return $this->response(json_encode($response, $jsonEncodeOptions), ['Content-Type' => 'application/json']);
    }

    protected function error($data = [])
    {

        return $this->json([], $data);
    }

    public function errorHandler(\Exception $e, $code)
    {
        return $this->json(
          [],
          [
            'code' => $code,
            'message' => $e->getMessage(),
          ]
        );
    }
}

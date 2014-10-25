<?php

namespace Openlaw;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected function response($data = '', $headers = [], $code = Response::HTTP_OK)
    {
        return new Response($data, $code, $headers);
    }

    protected function json($data = [])
    {
        return $this->response(json_encode(['content' => $data]), ['Content-Type' => 'application/json']);
    }

    protected function error($data = [])
    {
        return $this->response(json_encode($data, JSON_FORCE_OBJECT), ['Content-Type' => 'application/json']);
    }

    public function errorHandler(\Exception $e, $code)
    {
        switch ($code) {

        }

        $code2pass = Response::HTTP_OK;
        return $this->response(json_encode([
                'error' => [
                    'code' => $code,
                    'message' => $e->getMessage(),
                ]
            ]));
    }
}

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
        return $this->response(json_encode($data), ['Content-Type' => 'application/json']);
    }
}

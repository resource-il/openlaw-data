<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Booklet\Booklet as BookletData;

class Booklet extends Controller {
    public function index()
    {
        return 'Booklet index';
    }

    public function single(Request $request, Application $app, BookletData $booklet)
    {
        $include_parts = (bool) $request->query->get('include_parts', 0);
        if ($include_parts) {
            $booklet->loadParts();
        }
        return $this->json($booklet);
    }
}

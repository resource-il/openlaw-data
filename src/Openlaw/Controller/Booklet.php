<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Data\Booklet as BookletData;

class Booklet extends Controller {
    public function index()
    {
        return 'Booklet index';
    }

    public function single(Request $request, Application $app, BookletData $booklet)
    {
        return $this->json($booklet);
    }

    public function singleWithParts(Request $request, Application $app, BookletData $booklet)
    {
        return $this->json($booklet->loadParts());
    }

    public function singlePart(Request $request, Application $app, BookletData $booklet)
    {

    }
}

<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Data\Part as PartData;
use Openlaw\Data\Booklet as BookletData;

class Part extends Controller {
    public function index()
    {
        return 'Booklet part index';
    }

    public function single(Request $request, Application $app, $booklet, $part)
    {
        $booklet_part = PartData::factory($booklet, $part);
        return $this->json($booklet_part);
    }
}

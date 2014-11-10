<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Openlaw\Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Data\Part as PartData;

class Part extends Controller
{
    public function index()
    {
        return 'Booklet part index';
    }

    public function single(Request $request, Application $app, $booklet, $part)
    {
        $booklet_part = PartData::factory($booklet, $part);

        return $app->json($booklet_part);
    }
}

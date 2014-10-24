<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Booklet\Part as PartData;

class Booklet extends Controller {
    public function index()
    {
        return 'Booklet part index';
    }

    public function single(Request $request, Application $app, PartData $booklet_part)
    {
        return $this->json($booklet_part);
    }
}

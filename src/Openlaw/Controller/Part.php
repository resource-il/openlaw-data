<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Data\Part as PartData;

class Part extends Controller {
    public function index()
    {
        return 'Booklet part index';
    }

    public function single(Request $request, Application $app, PartData $booklet_part)
    {
        return $this->json($booklet_part);
    }
}

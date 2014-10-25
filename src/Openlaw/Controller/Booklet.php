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
        if (!$booklet->_id) {
            $app->abort(404, 'The booklet you looked for does not exist.');
        }
        return $this->json($booklet);
    }

    public function singleWithParts(Request $request, Application $app, BookletData $booklet)
    {
        return $this->json($booklet->loadParts());
    }

    public function dataset(Request $request, Application $app, array $booklets = [])
    {
        $part = $request->query->get('part', 0);
        if ($part && intval($part) == $part) {
            /** @var BookletData[] $booklets */

            foreach ($booklets as $key => $booklet) {
                $booklets[$key]->loadParts();
            }

        }
        return $this->json($booklets);
    }

    public function byKnesset(Request $request, Application $app, array $knesset = [])
    {
        return $this->dataset($request, $app, $knesset);
    }

    public function byYear(Request $request, Application $app, array $year = [])
    {
        return $this->dataset($request, $app, $year);
    }
}

<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Openlaw\Data\Booklet as BookletData;

class Booklet extends Controller
{
    public function index()
    {
        $usage = [
          '{booklet}/' => 'One booklet by booklet number',
          '{booklet}/part/' => 'One booklet with parts',
          '{booklet}/?part=1' => 'One booklet with parts',
          '{booklet}/part/{part}/' => 'One part of a booklet by booklet number and part number',
          'knesset/{knesset}/' => 'All booklets published during a specific knesset',
          'knesset/{knesset}/?part=1' => 'All booklets published during a specific knesset, with booklet parts',
          'year/{year}/' => 'All booklets published during a specific year',
          'year/{year}/?part=1' => 'All booklets published during a specific year, with booklet parts',
        ];

        return $this->json([], [], $usage);
    }

    public function indexKnesset()
    {
        $usage = [
          '{knesset}/' => 'All booklets published during a specific knesset',
          '{knesset}/?part=1' => 'All booklets published during a specific knesset, with booklet parts',
        ];

        return $this->json([], [], $usage);
    }

    public function indexYear()
    {
        $usage = [
          '{year}/' => 'All booklets published during a specific year',
          '{year}/?part=1' => 'All booklets published during a specific year, with booklet parts',
        ];

        return $this->json([], [], $usage);
    }

    public function single(Request $request, Application $app, BookletData $booklet)
    {
        if (!$booklet->_id) {
            $app->abort(404, 'The booklet you looked for does not exist.');
        }

        $part = $request->query->get('part', 0);
        if ($part && intval($part) == $part) {
            $booklet->loadParts();
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

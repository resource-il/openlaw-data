<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Openlaw\Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Openlaw\Data\Booklet as BookletData;

class Booklet extends Controller
{
    /**
     * @param Application $app
     * @return JsonResponse
     */
    public function index(Application $app)
    {
        $usage = [
          '{booklet}/' => 'One booklet by booklet number',
          '{booklet}/part' => 'One booklet with parts',
          '{booklet}/?part=1' => 'One booklet with parts',
          '{booklet}/part/{part}' => 'One part of a booklet by booklet number and part number',
          'knesset/{knesset}' => 'All booklets published during a specific knesset',
          'knesset/{knesset}?part=1' => 'All booklets published during a specific knesset, with booklet parts',
          'year/{year}' => 'All booklets published during a specific year',
          'year/{year}?part=1' => 'All booklets published during a specific year, with booklet parts',
        ];

        return $app->json([], [], $usage);
    }

    /**
     * @param Application $app
     * @return JsonResponse
     */
    public function indexKnesset(Application $app)
    {
        $usage = [
          '{knesset}' => 'All booklets published during a specific knesset',
          '{knesset}?part=1' => 'All booklets published during a specific knesset, with booklet parts',
        ];

        return $app->json([], [], $usage);
    }

    /**
     * @param Application $app
     * @return JsonResponse
     */
    public function indexYear(Application $app)
    {
        $usage = [
          '{year}' => 'All booklets published during a specific year',
          '{year}?part=1' => 'All booklets published during a specific year, with booklet parts',
        ];

        return $app->json([], [], $usage);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @param int $booklet
     * @return JsonResponse
     */
    public function single(Request $request, Application $app, $booklet)
    {
        $bookletData = BookletData::factory($booklet);

        if (!$bookletData->_id) {
            $app->abort(404, 'The booklet you looked for does not exist.');
        }

        $part = $request->query->get('part', 0);
        if ($part && intval($part) == $part) {
            $bookletData->loadParts();
        }

        return $app->json($bookletData);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @param int $booklet
     * @return JsonResponse
     */
    public function singleWithParts(Request $request, Application $app, $booklet)
    {
        $bookletData = BookletData::factory($booklet);
        return $app->json($bookletData->loadParts());
    }

    /**
     * @param Request $request
     * @param Application $app
     * @param array $booklets
     * @return JsonResponse
     */
    public function dataset(Request $request, Application $app, array $booklets = [])
    {
        $part = $request->query->get('part', 0);
        if ($part && intval($part) == $part) {
            /** @var BookletData[] $booklets */

            foreach ($booklets as $key => $booklet) {
                $booklets[$key]->loadParts();
            }

        }

        return $app->json($booklets);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @param int $knesset
     * @return JsonResponse
     */
    public function byKnesset(Request $request, Application $app, $knesset = 0)
    {
        $knessetBooklets = BookletData::factoryKnesset($knesset);
        return $this->dataset($request, $app, $knessetBooklets);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @param int $year
     * @return JsonResponse
     */
    public function byYear(Request $request, Application $app, $year = 0)
    {
        $yearBooklets = BookletData::factoryYear($year);
        return $this->dataset($request, $app, $yearBooklets);
    }
}

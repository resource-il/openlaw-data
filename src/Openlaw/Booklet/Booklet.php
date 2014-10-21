<?php

namespace Openlaw\Booklet;

use Openlaw\Collection;

class Booklet extends Collection
{
    protected static $collection = 'booklet';

    public static function factory($booklet = 0)
    {
        $query = [];
        if (!empty($booklet) && intval($booklet) == $booklet) {
            $query = ['booklet' => $booklet];
        }
        return parent::factory($query);
    }

    protected $schema = [
      'booklet' => 0,
      'knesset' => 0,
      'dates' => [
        'published' => 0,
      ],
      'links' => [
        'knesset_single_page' => null,
        'justice_pdf' => null,
        'justice_single_page' => null,
      ],
      'origin' => [
        'justice_description' => null,
      ],
      'created' => 0,
      'updated' => 0,
    ];
}

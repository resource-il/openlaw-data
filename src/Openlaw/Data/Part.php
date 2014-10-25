<?php

namespace Openlaw\Data;

use Openlaw\Collection;

class Part extends Collection
{
    protected static $collectionName = 'booklet_part';

    public static function factory($booklet = 0, $part = 0)
    {
        $query = [];
        if (!empty($booklet) && intval($booklet) == $booklet) {
            $query['booklet'] = (int) $booklet;
            if (!empty($part) && intval($part) == $part) {
                $query['origin.knesset_part'] = (string) $part;
            }
        }
        return parent::factory($query);
    }

    protected $schema = [
      'booklet' => 0,
      'type' => null,
      'title' => null,
      'indirect' => [],
      'dates' => [
        'accepted' => 0,
      ],
      'links' => [
        'knesset_pdf' => null,
      ],
      'origin' => [
        'knesset_title' => null,
        'knesset_part' => 0,
      ],
      'created' => 0,
      'updated' => 0,
    ];
}

<?php

namespace Openlaw\Booklet;

use Openlaw\Collection;

class Part extends Collection
{
    protected static $collection = 'booklet_part';

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

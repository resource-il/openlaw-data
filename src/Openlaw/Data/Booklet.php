<?php

namespace Openlaw\Data;

use Openlaw\Collection;

class Booklet extends Collection
{
    protected static $collectionName = 'booklet';

    /**
     * @param int $booklet
     * @return Booklet
     */
    public static function factory($booklet = 0)
    {
        $query = [];
        if (!empty($booklet) && intval($booklet) == $booklet) {
            $query = ['booklet' => (int) $booklet];
        }

        return parent::factory($query);
    }

    /**
     * @param int $year
     * @return array
     */
    public static function factoryYear($year = 0)
    {
        $query = ['dates.published' => ['$regex' => new \MongoRegex('/^' . $year . '/i')]];

        return static::factoryMultiple($query);
    }

    /**
     * @param int $knesset
     * @return array
     */
    public static function factoryKnesset($knesset = 0)
    {
        $query = ['knesset' => (int) $knesset];

        return static::factoryMultiple($query);
    }

    /**
     * @var array
     */
    protected $schema = [
      '_id' => null,
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

    /**
     * @return array
     */
    public function getParts()
    {
        if (empty($this->booklet)) {
            return array();
        }

        return Part::factoryMultiple(['booklet' => (int) $this->booklet]);
    }

    /**
     * @return $this
     */
    public function loadParts()
    {
        $this->schema['parts'] = [];
        $this->parts = $this->getParts();

        return $this;
    }
}

<?php

namespace Openlaw;

use SebastianBergmann\Exporter\Exception;

abstract class Collection
{
    protected static $database = 'openlaw';
    protected static $collection = null;

    /**
     * @param array $query
     * @return Collection
     */
    public static function factory($query = [])
    {
        if (empty(static::$collection)) {
            return null;
        }
        $mongoCollection = (new \MongoClient())->selectCollection(static::$database, static::$collection);

        return new static($mongoCollection, $query);
    }

    protected $mongoCollection = null;
    protected $data = [];
    protected $schema = [];

    protected function __construct(\MongoCollection $mongoCollection = null, $query = [])
    {
        $this->mongoCollection = $mongoCollection;
        if (!empty($query)) {
            $this->data = $this->mongoCollection->findOne($query);
            if (empty($this->data)) {
                $this->data = $query;
            }
        }
    }

    public function __get($name)
    {
        if (isset($this->schema[$name])) {
            return isset($this->data[$name]) ? $this->data[$name] : $this->schema[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (isset($this->schema[$name])) {
            $this->data[$name] = $value;
        }
    }

    public function save()
    {
        $this->mongoCollection->save($this->data);
    }

    public function pack()
    {
        return $this->data;
    }

    public function unpack($array = [])
    {
        if (!is_array($array)) {
            throw new Exception(get_called_class() . '::unpack() must be provided with an associated array.');
        }

        if (empty($array)) {
            return;
        }

        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
    }
}

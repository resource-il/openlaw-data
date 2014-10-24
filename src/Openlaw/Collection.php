<?php

namespace Openlaw;

use SebastianBergmann\Exporter\Exception;

abstract class Collection implements \JsonSerializable
{
    /** @var \MongoClient $client */
    protected static $client = null;
    /** @var \MongoDB $database */
    protected static $database = null;
    protected static $databaseName = 'openlaw';
    protected static $collectionName = null;

    /**
     * @param array $query
     * @return Collection
     */
    public static function factory($query = [])
    {
        return new static(static::init(), $query);
    }

    public static function factoryMultiple($query = [])
    {
        /** @var \MongoCursor $collection */
        $collection = static::init()->find($query);

        $multiple = [];
        $recs = [];

        foreach ($collection as $record) {
            $recs[] = $record;
            $multiple[] = static::factory()->unpack($record);
        }

        return $multiple;
    }

    protected static function init()
    {
        if (empty(static::$collectionName)) {
            return null;
        }

        if (static::$client === null) {
            static::$client = new \MongoClient();
        }

        if (static::$database === null) {
            static::$database = static::$client->selectDB(static::$databaseName);
        }

        return static::$database->selectCollection(static::$collectionName);
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

    public function load($query)
    {
        if (!empty($query)) {
            $this->data = $this->mongoCollection->findOne($query);
            if (empty($this->data)) {
                $this->data = $query;
            }
        }

        return $this;
    }

    public function save()
    {
        $this->mongoCollection->save($this->data);

        return $this;
    }

    public function pack($filter = [])
    {
        return $this->data;
    }

    public function unpack($array = [])
    {
        if (!is_array($array)) {
            throw new Exception(get_called_class() . '::unpack() must be provided with an associated array.');
        }

        if (empty($array)) {
            return $this;
        }

        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function jsonSerialize()
    {
        $newData = $this->data;
        unset($newData['_id']);

        return $newData;
    }

    public function __get($name)
    {
        if (isset($this->schema[$name]) || $name == '_id') {
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

    public function __isset($name)
    {
        return (isset($this->schema[$name]) || $name == '_id') && isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}

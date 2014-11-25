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

    /**
     * @param array $query
     * @return array
     */
    public static function factoryMultiple($query = [])
    {
        /** @var \MongoCursor $collection */
        $collection = static::init()->find($query)->sort(['booklet' => 1]);

        $multiple = [];
        foreach ($collection as $record) {
            $multiple[] = static::factory()->unpack($record);
        }

        return $multiple;
    }

    /**
     * @return \MongoCollection
     */
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

    /**
     * @param \MongoCollection $mongoCollection
     * @param array $query
     */
    protected function __construct(\MongoCollection $mongoCollection = null, $query = [])
    {
        $this->mongoCollection = $mongoCollection;
        if (!empty($query)) {
            $this->data = $this->mongoCollection->findOne($query);
        }
    }

    /**
     * @param $query
     * @return $this
     */
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

    /**
     * @return $this
     */
    public function save()
    {
        $this->mongoCollection->save($this->data);

        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function unpack($array = [])
    {
        if (!is_array($array)) {
            throw new Exception(get_called_class() . '::unpack() must be provided an associated array.');
        }

        if (!empty($array)) foreach ($array as $key => $value) {
            // Use the magic method so other properties are not overridden
            $this->__set($key, $value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $newData = $this->data;
        unset($newData['_id']);

        return $newData;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->schema)) {
            return array_key_exists($name, $this->data) ? $this->data[$name] : $this->schema[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->schema)) {
            $this->data[$name] = $value;
        }
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->schema) && array_key_exists($name, $this->data);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}

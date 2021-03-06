<?php

/*
 * This file is part of php-cache\mongodb-adapter package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Adapter\MongoDB;

use Cache\Adapter\Common\AbstractCachePool;
use Cache\Adapter\Common\CacheItem;
use Psr\Cache\CacheItemInterface;
use MongoDB\Collection;
use MongoDB\Driver\Manager;
use MongoDB\BSON\UTCDateTime;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MongoDBCachePool extends AbstractCachePool
{
    /**
     * @var Client
     */
    private $collection;

    /**
     *
     * @param Client $client
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public static function createCollection(Manager $manager, $namespace)
    {
        $collection = new Collection($manager, $namespace);
        $collection->createIndex(['expireAt' => 1], ['expireAfterSeconds' => 0]);

        return $collection;
    }

    protected function fetchObjectFromCache($key)
    {
        $object = $this->collection->findOne(['_id' => $key]);

        if ($object && isset($object->data)) {
            $item = new CacheItem($key, true, unserialize($object->data));

            if (isset($object->expiresAt)) {

                $item->expiresAt($object->expiresAt->toDateTime());
            }

            return $item;
        }

        return false;
    }

    protected function clearAllObjectsFromCache()
    {
        $this->collection->deleteMany([]);

        return true;
    }

    protected function clearOneObjectFromCache($key)
    {
        $this->collection->deleteOne(['_id' => $key]);

        return true;
    }

    protected function storeItemInCache($key, CacheItemInterface $item, $ttl)
    {
        $object = [
            '_id' => $key, 
            'data' => serialize($item->get()),
        ];

        if ($ttl) {
            $object['expiresAt'] = new UTCDateTime((time() + $ttl) * 1000);
        }

        $this->collection->updateOne(['_id' => $key], ['$set' => $object], ['upsert' => true]);

        return true;
    }
}

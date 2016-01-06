<?php

/*
 * This file is part of php-cache\mongo-adapter package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Adapter\Mongo\Tests;

use Cache\Adapter\Mongo\MongoCachePool;
use Cache\IntegrationTests\TaggableCachePoolTest;

class IntegrationTagTest extends TaggableCachePoolTest
{
    public function createCachePool()
    {
        return new MongoCachePool();
    }
}

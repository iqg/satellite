<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 12/15/15
 * Time: 14:42
 */

namespace DWD\SatelliteBundle\Util\Cache;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class CacheHandler
{
    private $container;
    private $redis;

    public function __construct(Container $container){
        $this->container = $container;

        $this->redis = $this->container->get('snc_redis.cache');
    }

    public function cacheDataWithRedis($arrayData, $key, $ttl)
    {
        $this->redis->setex($key, $ttl, json_encode($arrayData));

        return $arrayData;
    }

    public function getCachedData($key)
    {
        if($this->redis->exists($key)) {
            return json_decode($this->redis->get($key), true);
        }

        return null;
    }
}
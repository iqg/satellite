<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 10/31/14
 * Time: 2:39 PM
 */

namespace DWD\SatelliteBundle\Util\SystemConfig;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class SystemConfigHandler
{
    private $em;
    private $redis;
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->redis = $container->get('snc_redis.cache');
    }

    /**
     * @param $config
     */
    public function getConfig($config)
    {
        $configKey = strtoupper($config);

        if($this->redis->exists($configKey)) {
            return $this->redis->get($configKey);
        } else {
            $config = strtolower($config);
            $config_value = $this->getSystemConfigViaInternalApi($config);

            $this->redis->setex($configKey, 1800, $config_value);
            return $config_value;
        }
    }

    protected function getSystemConfigViaInternalApi($key)
    {
        /**@var $internalApiHandler \DWD\SatelliteBundle\Util\InternalApi\InternalApiHandler*/
        $internalApiHandler = $this->container->get('internalapi.handler');

        $parameters = ['key' => $key];
        $internalData = $internalApiHandler->internalApiRequest('get', '/systemconfig/getconfig', $parameters);

        return isset($internalData['config_value']) ? $internalData['config_value'] : null;
    }

    /**
     * @return \DateTime
     */
    public function getLastResetTime()
    {
        $tmp_time = new \DateTime(date('Y-m-d') . ' ' . $this->getConfig('campaign_reset_time'));
        $now_time = new \DateTime('now');
        if ($now_time > $tmp_time)
            return $tmp_time;
        else
            return $tmp_time->modify('-1 day');
    }

    /**
     * @return int
     */
    public  function getNextResetTime()
    {
        $reset_time = $this->getLastResetTime();

        /**@var $next_reset_time \DateTime*/
        $next_reset_time = clone $reset_time;
        $next_reset_time->modify('+1 day');

        return $next_reset_time->getTimestamp();
    }
}

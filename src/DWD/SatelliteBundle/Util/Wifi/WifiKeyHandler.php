<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 1/19/16
 * Time: 09:36
 */

namespace DWD\SatelliteBundle\Util\Wifi;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class WifiKeyHandler {
    /**@var $this->container \Symfony\Bundle\FrameworkBundle\Controller\Controller*/

    protected $container;

    protected $redis;

    protected $appId;
    protected $secret;
    protected $token;

    public function __construct(Container $container){
        $this->container = $container;

        $this->redis = $this->container->get('snc_redis.cache');

        $wifiKeyConfig = $this->container->getParameter('wifi_key');
        $this->appId = isset($wifiKeyConfig['appid']) ? $wifiKeyConfig['appid'] : null;
        $this->secret = isset($wifiKeyConfig['secret']) ? $wifiKeyConfig['secret'] : null;
        $this->token = isset($wifiKeyConfig['token']) ? $wifiKeyConfig['token'] : null;
    }

    /**
     * @return array|null
     */
    public function getWifiKeyAccessTokenViaApi()
    {
        /**@var $dataHttp \DWD\SatelliteBundle\Util\InternalApi\DWDDataHttp*/
        $dataHttp = $this->container->get('dwd.data.http');

        $nonce = rand(100000, 999999);
        $timestamp = (new \DateTime())->getTimestamp();

        /**@var $signHandler \DWD\SatelliteBundle\Util\Wifi\Sign*/
        $signHandler = $this->container->get('sign.handler');
        $signature = $signHandler->generateSignature($this->appId, $this->secret, $timestamp, $nonce);

        $key = 'data';
        $data = array(
            array(
                'url'           => 'http://openapi.o2o.lianwifi.com/sp/token',
                'method'        => 'get',
                'key'           => 'wifi',
                'data'          => array(
                    'grant_type'    => 'client_credential',
                    'appid'         => $this->appId,
                    'secret'        => $this->secret,
                    'timestamp'     => $timestamp,
                    'nonce'         => $nonce,
                    'signature'     => $signature
                ),
            ),
        );

        $data = $dataHttp->MutliCall($data);

        $responseData = $data['wifi'];

        $responseData += ['errno' => ''];

        $errno = $responseData['errno'];
        if($errno == 0) {
            return $responseData;
        }

        return null;
    }

    /**
     * @return null
     */
    public function refreshWifiKeyAccessToken()
    {
        $wifiKeyResult = $this->getWifiKeyAccessTokenViaApi();

        $wifiKeyResult += [
            'access_token'  => '',
            'expires_in'    => ''
        ];

        $accessToken = $wifiKeyResult['access_token'];
        $expiresIn = $wifiKeyResult['expires_in'];

        if($accessToken && $expiresIn) {
            $redisKey = 'S_WIFI_KEY_ACCESS_TOKEN';

            $this->redis->setex($redisKey, $expiresIn, $accessToken);

            return $accessToken;
        }

        return null;
    }

    public function getWifiKeyAccessToken()
    {
        $redisKey = 'S_WIFI_KEY_ACCESS_TOKEN';

        if($this->redis->exists($redisKey)) {
            $token = $this->redis->get($redisKey);
        } else {
            $token = $this->refreshWifiKeyAccessToken();
        }

        return $token;
    }

    public function postWifiKeyList($uuid, $wifiKeyList)
    {
        /**@var $dataHttp \DWD\SatelliteBundle\Util\InternalApi\DWDDataHttp*/
        $dataHttp = $this->container->get('dwd.data.http');

        $accessToken = $this->getWifiKeyAccessToken();

        $key = 'data';
        $data = array(
            array(
                'url'               => 'http://openapi.o2o.lianwifi.com/callback/senddata?access_token=' . $accessToken,
                'method'            => 'post',
                'key'               => 'wifi',
                'data'              => array(
                    'uuid'          => $uuid,
                    'display_type'  => 10003,
                    'body'      => $wifiKeyList,
                ),
            ),
        );

        $this->container->get('monolog.logger.access')->info( 'post request: ' . json_encode( $data ) );

        $responseData = $dataHttp->request($data);
//        $responseData = $data['wifi'];

        $this->container->get('monolog.logger.access')->info( 'post return :' . json_encode($responseData) );
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 1/12/16
 * Time: 17:06
 */

namespace DWD\SatelliteBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/api")
 */
class OpenWifiController extends Controller {
    /**
     * @Route("/wifi_key/request")
     * @Method({"GET"})
     * @ApiDoc(
     *      section="第三方",
     *      description="给wifi万能钥匙的第三方接口",
     *      parameters={
     *        {"name"="signature",  "dataType"="string",    "required"=true},
     *        {"name"="timestamp",  "dataType"="string",    "required"=true},
     *        {"name"="nonce",      "dataType"="string",    "required"=true},
     *      }
     * )
     */
    public function getWifiKey(Request $request)
    {
        /**@var $wifiKeyHandler \DWD\SatelliteBundle\Util\Wifi\WifiKeyHandler*/
        $wifiKeyHandler = $this->container->get('wifikey.handler');
        $wifiKeyHandler->getWifiKeyAccessToken();

        $wifiKeyConfig = $this->container->getParameter('wifi_key');
        $appId = isset($wifiKeyConfig['appid']) ? $wifiKeyConfig['appid'] : null;
        $secret = isset($wifiKeyConfig['secret']) ? $wifiKeyConfig['secret'] : null;
        $token = isset($wifiKeyConfig['token']) ? $wifiKeyConfig['token'] : null;

        $this->container->get('monolog.logger.access')->info( json_encode( $request->query->all() ) );

        if($token) {
            $signature = $request->query->get('signature');
            $timestamp = $request->query->get('timestamp');
            $nonce = $request->query->get('nonce');

            $uuid = $request->query->get('uuid');

//            $this->container->get('monolog.logger.access')->info( $timestamp . '   ' . $nonce );

            /**@var $signHandler \DWD\SatelliteBundle\Util\Wifi\Sign*/
            $signHandler = $this->container->get('sign.handler');
            if($signHandler->checkSignature($signature, $token, $timestamp, $nonce)) {
                $wifiKeyList = $this->getWifiKeyList();
                $this->container->get('monolog.logger.access')->info( json_encode( $uuid ) );

                $wifiKeyHandler->postWifiKeyList($uuid, $wifiKeyList);
                $this->container->get('monolog.logger.access')->info( 'echo' );

                echo 'success';
                exit;
            }
        }

        return new JsonResponse();
    }

    protected  function getWifiKeyList() {
        $responseArray = [];

        /**@var $systemConfigHandler \DWD\SatelliteBundle\Util\SystemConfig\SystemConfigHandler*/
        $systemConfigHandler = $this->container->get('systemconfig.handler');
        $promotedIds = $systemConfigHandler->getConfig('wifikey_promoted_campaign_branch_ids');
        if($promotedIds) {
            /**@var $cacheHandler \DWD\SatelliteBundle\Util\Cache\CacheHandler*/
            $cacheHandler = $this->get('cache.handler');

            $redisKey = 'S_WIFIKEY_PROMOTED_CAMPAIGN_BRANCH_LIST';
            $responseArray = $cacheHandler->getCachedData($redisKey);
            if(!$responseArray) {
                $responseArray = $cacheHandler->cacheDataWithRedis($this->getWifiKeyListViaInternalApi($promotedIds), $redisKey, 3600);
            }

            if(count($responseArray) > 3) {
                shuffle($responseArray);
                $responseArray = array_slice($responseArray, 0, 3);
            }
        }

        return $responseArray;
    }

    protected function getWifiKeyListViaInternalApi($campaignBranchIds)
    {
        /**@var $internalApiHandler \DWD\SatelliteBundle\Util\InternalApi\InternalApiHandler*/
        $internalApiHandler = $this->container->get('internalapi.handler');

        $parameters = ['campaignBranchIds' => $campaignBranchIds];
        $internalData = $internalApiHandler->internalApiRequest('get', '/campaignbranch/wifikeylist', $parameters);

        $wifiKeyList = array();

        /**@var $systemConfigHandler \DWD\SatelliteBundle\Util\SystemConfig\SystemConfigHandler*/
        $systemConfigHandler = $this->container->get('systemconfig.handler');
        $h5Host = $systemConfigHandler->getConfig('h5_host_for_auth_code');

        $internalItemList = $internalData['list'];
        foreach($internalItemList as $k => $dataItem) {
            $base_array = array(
                //'display_type'  =>  strval(10003),
                'title'         =>  $dataItem['name'],
                'desc'          =>  $dataItem['description'],
                'img'           =>  $dataItem['images'][0],
                'url'           =>  'http://' . $h5Host . '/item/' . $dataItem['id'] . '?type=bargain',
//                'description'   =>  $dataItem['description'],
//                'city'          =>  '',
                'original_price'=>  $dataItem['start_price'] * 100,
                'present_price'=>   $dataItem['current_price'] * 100,
                'starttime'      =>  0,
                'endtime'       =>  0,
            );

            $wifiKeyList[] = $base_array;
        }

        return $wifiKeyList;
    }
}
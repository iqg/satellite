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


/**
 * @Route("/api")
 */
class WifiKeyController extends Controller {
    /**
     * @Route("/wifi_key/get")
     * @Method({"GET"})
     * @ApiDoc(
     *      section="第三方",
     *      description="给wifi万能钥匙的第三方接口",
     *      parameters={
     *        {"name"="lat",        "dataType"="float",     "required"=false},
     *        {"name"="long",       "dataType"="float",     "required"=false},
     *        {"name"="signature",  "dataType"="string",    "required"=true},
     *        {"name"="timestamp",  "dataType"="string",    "required"=true},
     *        {"name"="nonce",      "dataType"="string",    "required"=true},
     *        {"name"="openid",     "dataType"="string",    "required"=true},
     *        {"name"="phone",      "dataType"="string",    "required"=false},
     *        {"name"="keyword",    "dataType"="string",    "required"=true},
     *        {"name"="category",   "dataType"="string",    "required"=false},
     *        {"name"="coordtype",  "dataType"="string",    "required"=false,   "format"="baidu|gaode"},
     *        {"name"="range",      "dataType"="integer",   "required"=true},
     *        {"name"="limit",      "dataType"="integer",   "required"=false},
     *      }
     * )
     */
    public function getWifiKeyListAction() {
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

        return new JsonResponse($responseArray);
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
                'display_type'  =>  strval(10003),
                'title'         =>  $dataItem['name'],
                'desc'          =>  $dataItem['description'],
                'img'           =>  $dataItem['images'][0],
                'url'           =>  'http://' . $h5Host . '/item/' . $dataItem['id'] . '?type=bargain',
                'description'   =>  $dataItem['description'],
                'city'          =>  '',
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
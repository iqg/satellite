<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 12/22/15
 * Time: 17:03
 */

namespace DWD\SatelliteBundle\Util\InternalApi;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InternalApiHandler
{
    private $container;

    private $em;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function addPaginationParameters($parameters, $lastId, $limit)
    {
        $parameters['needPagination'] = 1;
        $parameters['pageNum'] = ceil(floatval($lastId/$limit)) + 1;
        $parameters['pageLimit'] = $limit;

        return $parameters;
    }

    public function internalApiRequest($method, $path, $parameters)
    {
        $internalApiHost = 'http://' . $this->container->getParameter('internal_api_host');

        /**@var $dataHttp \DWD\SatelliteBundle\Util\InternalApi\DWDDataHttp*/
        $dataHttp = $this->container->get('dwd.data.http');

        $key = 'data';
        $data = array(
            array(
                'url'    => $internalApiHost.$path,
                'data'   => $parameters,
                'method' => $method,
                'key'    => $key,
            ),
        );

        $data = $dataHttp->MutliCall($data);
        $items = $data[$key];

        if(isset($items['errno'])) {
            $errno = $items['errno'];

            if($errno == 0) {
                return $items['data'];
            }
        }

        return [];
    }
}
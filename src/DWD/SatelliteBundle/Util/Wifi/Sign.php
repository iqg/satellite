<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 1/18/16
 * Time: 16:58
 */

namespace DWD\SatelliteBundle\Util\Wifi;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Sign
{
    protected $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    public function checkSignature($signature, $token, $timestamp, $nonce)
    {
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $signStr = implode( $tmpArr );
        $signStr = sha1( $signStr );

//        $this->container->get('monolog.logger.access')->info( $signStr . '  ' . $signature );

        return $signature == $signStr;
    }

    public function generateSignature($appId, $secret, $timestamp, $nonce)
    {
        $signKeys = array($appId, $secret, $timestamp, $nonce);

        $this->container->get('monolog.logger.access')->info( 'sign ar: ' . json_encode($signKeys) );

        sort($signKeys, SORT_STRING);
        $signStr = implode( $signKeys );
        $signStr = sha1( $signStr );


        $this->container->get('monolog.logger.access')->info( 'sign: ' . $signStr );


        return $signStr;
    }
}
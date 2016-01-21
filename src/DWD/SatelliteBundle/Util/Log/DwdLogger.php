<?php
/**
 * Created by PhpStorm.
 * User: caowei
 * Date: 15-7-03
 * Time: 10:05
 */

namespace DWD\SatelliteBundle\Util\Log;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;

class DwdLogger
{
    //容器
    private $container;

    //notice日志信息
    private $notice          = array();

    //http called record
    private $calledRecord    = array();

    public function __construct($key) 
    {
        $this->container     = $key;
    }

    public function addCalledRecord( $key, $value )
    {
        if( false == is_string( $key ) ){
            return false;
        }
        $this->calledRecord[$key] = $value;
        return true;
    }

    public function calledRecord()
    {
        $this->container->get('monolog.logger.callRecord')->info( json_encode( $this->calledRecord ) );

    }

    public function addNotice($key, $value)
    {
        if( false == is_string( $key ) ){
            return false;
        }
        $this->notice[$key] = $value;
        return true;
    }

    public function notice(){
        $this->container->get('monolog.logger.access')->info( json_encode( $this->notice ) );
    }

    public function addRequestLog(Request $request){
        $logInfo = array(
                       'method'      => $request->getMethod(), 
                       'request'     => $request->request->all(),
                       'query'       => $request->query->all(),
                       'cookie'      => $request->cookies->all(),
                       'header'      => $request->headers->all(),
                       'path'        => $request->getPathInfo(),
                    );
        $this->addNotice( 'request', $logInfo );

    }
}
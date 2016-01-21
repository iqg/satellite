<?php
/**
 * Created by PhpStorm.
 * User: zhangchao
 * Date: 8/24/15
 * Time: 17:48
 */

namespace DWD\SatelliteBundle\Util\InternalApi;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class DWDDataHttp
{
    private $em;
    private $redis;


    public function __construct(Container $container)
    {
        $this->em = $container->get('doctrine')->getManager();
        $this->redis = $container->get('snc_redis.cache');

    }

    static function callback($data, $delay) {
        usleep($delay);
        return $data;
    }

    static function PackageGetRequest( &$ch, $request ){
        $path            =  http_build_query( $request['data'] );
        $request['url'] .= '?' . $path;
        curl_setopt($ch, CURLOPT_URL, $request['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
    }

    static function PackagePostRequest( &$ch, $request ){
        curl_setopt($ch, CURLOPT_URL, $request['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['data']);
    }

    static function MutliCall($requests, $delay = 0) {

        $queue                 = curl_multi_init();
        $map                   = array();

        foreach ($requests as $reqId => $request) {
            $ch                = curl_init();
            switch ( $request['method'] ) {
                case 'get':
                    self::PackageGetRequest( $ch, $request );
                    break;
                case 'post':
                    self::PackagePostRequest( $ch, $request );
                    break;
                default: break;
            }
            self::PackageGetRequest( $ch, $request );
            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $request['key'];
        }

        $responses        = array();

        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if ($code != CURLM_OK) { break; }

            // a request was just completed -- find out which one
            while ($done  = curl_multi_info_read($queue)) {

                // get the info and content returned on the request
                $info     = curl_getinfo($done['handle']);
                $error    = curl_error($done['handle']);
                $results  = self::callback(curl_multi_getcontent($done['handle']), $delay);

                if( empty( $error ) ){
                    $responses[$map[(string) $done['handle']]] = json_decode( $results, true );
                } else {
                    $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results');
                }
                // remove the curl handle that just completed
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }

        } while ($active);

        curl_multi_close($queue);

        return $responses;
    }

    static function request($request) {
        $ch                = curl_init();
        switch ( $request['method'] ) {
            case 'get':
                self::PackageGetRequest( $ch, $request );
                break;
            case 'post':
                self::PackagePostRequest( $ch, $request );
                break;
            default: break;
        }
        self::PackageGetRequest( $ch, $request );

        $responses = curl_exec($ch);
        curl_close($ch);

        return $responses;
    }
}
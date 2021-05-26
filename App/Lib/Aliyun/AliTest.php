<?php
/**
 * @date   2021-05-26
 */

namespace App\Lib\Aliyun;


use App\Helper\AppLog;
use EasySwoole\EasySwoole\Config;

class AliTest
{
    public static function test()
    {

        $config = [
            'endpoint'  => 'ap-southeast-1.log.aliyuncs.com',
            'accessId'  => 'LTAI4GCPx',
            'secretKey' => 'ZAlT58',
            'project'   => 'dev-log'
        ];

        $log = new AliLog($config);
        $r   = $log->put(['apiMethodName' => 'test', 'apiRequestData' => 'xxx'],
            'dev-store',
            'dev-log',
        );

        AppLog::debug("写日志请求ID $r");


        $config = [
            'endpoint'  => 'oss-ap-southeast-1.aliyuncs.com',
            'accessId'  => 'LTAI4G',
            'secretKey' => 'EC5GLrV',
            'bucket'    => 'dev-oss',
        ];

        $oss = new AliOss($config);
        $r   = $oss->put("/test/a1.json", json_encode(["hello" . rand(100, 999)]), 'loan-sg-test');
        AppLog::debug("写OSS  $r");

        $r = $oss->get("/test/a1.json", 'loan-sg-test');
        AppLog::debug("读OSS  $r");

    }
}

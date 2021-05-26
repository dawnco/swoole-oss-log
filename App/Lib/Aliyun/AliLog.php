<?php
/**
 * @author Hi Developer
 * @date   2021-05-21
 */

namespace App\Lib\Aliyun;

use App\Exception\AppException;
use Extend\Ali\Log\Content;
use Extend\Ali\Log\Log;
use Extend\Ali\Log\LogGroup;

class AliLog extends Aliyun
{
    /**
     * 阿里云日志文档 https://help.aliyun.com/document_detail/29026.html
     * @param $data
     */
    public function put($data, $store, $project = null)
    {

        if (!$project) {
            $project = $this->config['project'];
        }

        if (!$project) {
            throw new AppException('没指定 AliLog project');
        }


        $log = new Log();
        $log->setTime(time());
        $temp = [];
        foreach ($data as $k => $v) {
            $content = new Content();
            $content->setKey($k);
            $content->setValue($v);
            $temp[] = $content;
        }
        $log->setContents($temp);

        $logGroup = new LogGroup();
        $logGroup->setLogs([$log]);
        $raw = $logGroup->serializeToString();
        $ret = $this->log($raw, $store, $project);

        return $ret['header']['x-log-requestid'];

    }

    private function log($data, $store, $project = null)
    {
        // https://help.aliyun.com/document_detail/29012.html

        $size = strlen($data);

        $header['Date']                  = gmdate('D, d M Y H:i:s T');
        $header['Content-Type']          = "application/x-protobuf";
        $header['Host']                  = $project . "." . $this->config['endpoint'];
        $header['Content-Length']        = $size;
        $header['Content-MD5']           = strtoupper(md5($data));
        $header['x-log-bodyrawsize']     = $size;
        $header['x-log-apiversion']      = "0.6.0";
        $header['x-log-signaturemethod'] = 'hmac-sha1';

        $resource = "/logstores/$store/shards/lb";

        $header['Authorization'] = $this->authorization($header, 'LOG', 'POST', $resource, ['x-log', 'x-acs']);

        $url = "https://$project.{$this->config['endpoint']}$resource";

        $ret = $this->request($url, 'POST', $header, $data);

        return $ret;
    }

}

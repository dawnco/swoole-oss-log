<?php
/**
 * @author Hi Developer
 * @date   2021-05-25
 */

namespace App\Lib\Aliyun;


use App\Exception\AppException;
use App\Exception\NetworkException;

class AliOss extends Aliyun
{


    /**
     * 保存到oss
     * @param $filename 路径  比如  /20210101/file/best.jpg
     * @param $data     文件内容
     * @param $bucket
     * @return 保存的url
     * @throws NetworkException
     */
    public function put($filename, $data, $bucket = null)
    {

        if (!$bucket) {
            $bucket = $this->config['bucket'];
        }
        if (!$bucket) {
            throw new AppException('没指定 OSS bucket');
        }

        // https://help.aliyun.com/document_detail/31955.html

        $date        = gmdate('D, d M Y H:i:s T');
        $contentType = $this->mime($filename);

        $md5    = base64_encode(md5($data, true));
        $header = [
            'Content-Length' => strlen($data),
            'Host'           => $bucket . "." . $this->config['endpoint'],
            'Content-MD5'    => $md5,
            'Content-Type'   => $contentType,
            'Date'           => $date,
        ];

        $canonicalizeResource = '/' . $bucket . $filename;


        $header['Authorization'] = $this->authorization($header, 'OSS', 'PUT', $canonicalizeResource, ['x-oss']);

        $url = "https://$bucket.{$this->config['endpoint']}" . $filename;

        $this->request($url, 'PUT', $header, $data);

        return $url;
    }

    /**
     * 获取文件内容
     * @param $filename 路径比如 /20210101/file/best.jpg
     * @param $bucket
     * @return string 文件内容
     * @throws NetworkException
     */
    public function get($filename, $bucket = null)
    {

        if (!$bucket) {
            $bucket = $this->config['bucket'];
        }
        if (!$bucket) {
            throw new AppException('没指定 OSS bucket');
        }


        $date = gmdate('D, d M Y H:i:s T');

        $header = [
            'Host'         => $bucket . "." . $this->config['endpoint'],
            'Content-MD5'  => '',
            'Content-Type' => '',
            'Date'         => $date,
        ];

        $canonicalizeResource = '/' . $bucket . $filename;


        $header['Authorization'] = $this->authorization($header, 'OSS', 'GET', $canonicalizeResource, ['x-oss']);

        $url = "https://$bucket.{$this->config['endpoint']}" . $filename;

        $r = $this->request($url, 'GET', $header);

        return $r['body'];
    }

}

<?php


namespace app\common\util;


use think\Loader;

Loader::import('cos-php-sdk-v5.index');

use think\process\Utils;

class CosUtil extends Utils
{

    function init()
    {
        $secretId = "AKIDiJgneGs24sQlrg4xKSM1tWt1zjEjR43m"; //"云 API 密钥 SecretId";
        $secretKey = "ctvjHgy1pMdfV6S8ankWVGl1C8pzua2G"; //"云 API 密钥 SecretKey";
        $region = "cd"; //设置一个默认的存储桶地域
        $cosClient = new \Qcloud\Cos\Client(
            array(
                'region' => $region,
                'schema' => 'https', //协议头部，默认为http
                'credentials' => array(
                    'secretId' => $secretId,
                    'secretKey' => $secretKey)));
        return $cosClient;
    }

    function uploadString($uuid, $str)
    {
        //上传文件
        //putObject(上传接口，最大支持上传5G文件)
        //上传内存中的字符串
        try {
            $bucket = "47-1256569009";
            //存储桶名称 格式：BucketName-APPID
            $key = $uuid;
            $result = $this->init()->putObject(array(
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => $str));
            return ($result);
        } catch (\Exception $e) {
            echo "$e\n";
        }
    }


    function download($key)
    {
        try {
            $result = $this->init()->getObject(array(
                'Bucket' => '47-1256569009',
                'Key' => $key
            ));
            // 请求成功
            return $result['Body'];
        } catch (\Exception $e) {
            // 请求失败
            echo($e);
        }
    }
}
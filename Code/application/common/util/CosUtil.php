<?php


namespace app\common\util;


use think\process\Utils;

class CosUtil extends Utils
{

    function init()
    {
        $secretId = "COS_SECRETID"; //"云 API 密钥 SecretId";
        $secretKey = "COS_SECRETKEY"; //"云 API 密钥 SecretKey";
        $region = "COS_REGION"; //设置一个默认的存储桶地域
        $cosClient = new Qcloud\Cos\Client(
            array(
                'region' => $region,
                'schema' => 'https', //协议头部，默认为http
                'credentials' => array(
                    'secretId' => $secretId,
                    'secretKey' => $secretKey)));

        return $cosClient;
    }

    function uploadString()
    {
        //上传文件
        //putObject(上传接口，最大支持上传5G文件)
        //上传内存中的字符串
        try {
            $bucket = "examplebucket-1250000000"; //存储桶名称 格式：BucketName-APPID
            $key = "exampleobject";
            $result = $this->init()->putObject(array(
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => 'Hello World!'));
            print_r($result);
        } catch (\Exception $e) {
            echo "$e\n";
        }
    }
}
<?php


namespace app\common\util;


use think\process\Utils;

class JsonUtil extends Utils
{

    /**
     * 返回统一格式
     * @param $code 状态码
     * @param $msg 信息
     * @param $data 数据
     * @return false|string
     */
    function jsonData($code, $msg, $data)
    {
        $msgArr = ['error', 'success'];
        return json_encode(['code' => $code, 'msg' => $msgArr[$msg], 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

    /**  base64格式编码转换为图片并保存对应文件夹 */
    function base64_image_content($base64_image_content)
    {
//        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            //匹配成功
            $image_name = uniqid() . '.' . $result[2];
            $image_file = "./Public/Personal/{$image_name}";
            //服务器文件存储路径
            if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return '/Public/Personal/' . $image_name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
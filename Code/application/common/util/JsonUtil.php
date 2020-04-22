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
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = tmpfile(str_replace('.', '', uniqid('', true)) . ".{$type}");
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                echo $new_file;
                return $new_file;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
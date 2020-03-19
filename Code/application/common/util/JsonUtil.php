<?php


namespace app\common\util;


class JsonUtil
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

}
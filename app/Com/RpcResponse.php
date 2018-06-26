<?php
/**
 * Created by PhpStorm.
 * User: Wen Peng
 * Date: 2018/3/31
 * Time: 上午10:26
 */

namespace App\Com;

use StdClass;
use Exception;
use Illuminate\Http\JsonResponse;

class RpcResponse
{
    /**
     * 返回正常响应(HTTP_CODE:200)
     *
     * @param array|object $data 数据主体
     * @param array $headers 自定义头信息
     * @return \Illuminate\Http\JsonResponse;
     */
    public static function success($data = null, array $headers = [])
    {
        if (null === $data) {
            $data = new StdClass();
        }

        // 为统一响应格式，这里强制约束为 JSON 对象
        return response()->json([
            'code' => 200,
            'data' => $data,
        ]);
    }


    /**
     * 返回异常响应(HTTP_CODE:500)
     *
     * @param array $errorData 预置错误
     * @param string $message 自定义消息
     * @param array $headers 自定义头信息
     * @return \Illuminate\Http\JsonResponse;
     * @throws Exception
     */
    public static function error(array $errorData, string $message = '', array $headers = [])
    {
        $data = static::errorBuilder($errorData, $message);

        return response()->json($data);
    }


    /**
     * 组装错误消息
     *
     * @param array $error 预置错误
     * @param string $message 自定义消息
     * @return array
     * @throws Exception
     */
    private static function errorBuilder(array $error, string $message = '')
    {
        // 防止 ErrorData 定义不规范，此处做下校验
        if ( !isset($error['code'], $error['message'])) {
            throw new Exception('Invalid RPC error code');
        }
        $data = [
            'code' => $error['code'],
            'message' => $error['message'],
        ];
        // 如果有自定义消息，则覆盖预置消息
        if ('' !== $message) {
            $data['message'] = $message;
        }

        return $data;
    }
}
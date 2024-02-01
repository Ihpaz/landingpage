<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ResponseFormat
{
    public static function ok($data, $message = 'Success')
    {
        $result_array = [
            'code' => 200,
            'message' => $message,
            'data' => $data
        ];

        return response($result_array, Response::HTTP_OK);
    }

    public static function created($data, $message = 'Data berhasil disimpan')
    {
        $result_array = [
            'code' => 201,
            'message' => $message,
            'data' => $data
        ];
        return response($result_array, Response::HTTP_CREATED);
    }

    public static function empty()
    {
        $result_array = [
            'code' => 204,
            'message' => "Hasil pencarian kosong"
        ];
        return response($result_array, Response::HTTP_NO_CONTENT);
    }

    public static function error($error, $message = 'Maaf terjadi kesealahan pada server')
    {
        $result_array = [
            'code' => 400,
            'message' => $message,
            'errors' => $error
        ];
        return response($result_array, Response::HTTP_FORBIDDEN);
    }

    public static function unauthorized()
    {
        $result_array = [
            'code' => 401,
            'message' => "Maaf anda tidak memiliki hak untuk mengakses resource ini"
        ];
        return response($result_array, Response::HTTP_UNAUTHORIZED);
    }

    public static function unauthenticated($message = 'Unauthenticated')
    {
        $result_array = [
            'code' => 401,
            'message' => $message
        ];
        return response($result_array, Response::HTTP_UNAUTHORIZED);
    }
}

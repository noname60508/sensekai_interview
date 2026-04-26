<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class responseServerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // apiResponseマクロの定義
        Response::macro('apiResponse', function ($data = [], $code = 200, $message = null) {
            // 2XX系はsuccess、その他はerror
            $status = $code >= 200 && $code < 300 ? 'success' : 'error';
            return Response::json([
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ], $code);
        });

        // apiFailマクロの定義
        Response::macro('apiFail', function ($message, $code = 500) {
            return Response::json([
                'status' =>  'error',
                'message' => env('APP_DEBUG') ? $message->getMessage() : null,
                'data' => [],
            ], $code);
        });

        // validationErrorマクロの定義
        Response::macro('validationError', function ($validator) {
            return Response::json([
                'status' => 'error',
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ], 400);
        });
    }
}

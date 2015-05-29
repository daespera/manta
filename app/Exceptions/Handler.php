<?php namespace App\Exceptions;

use Exception;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $payload = [];
        $payload['timestamp'] = date('Y-m-d H:i:s');

        if(get_class($e) != 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException') {
            $payload['http_code'] = 500;
            $payload['status'] = 'fail';
            $payload['message'] = $e->getMessage();
            $file = $e->getFile() !== null ? $e->getFile() : 'n/a';
            $line = $e->getLine() !== null ? $e->getLine() : 'n/a';
            $payload['debug']['type'] = get_class($e);
            $payload['debug']['file'] = $file.':'.$line;
        }
        else {
            $payload['http_code'] = 404;
            $payload['status'] = 'fail';
            $payload['message'] = 'Route not defined';
        }
        
        return response()->json($payload, $payload['http_code'], 
            [
                'Content-type'=> 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin'=>'*',
                'Access-Control-Allow-Methods'=> 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers'=>'Origin, Content-Type, X-Auth-Token'
            ], 
            JSON_UNESCAPED_UNICODE
        );
    }

}

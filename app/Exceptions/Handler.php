<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\ResponseHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //Handling resource not found
        if ($exception instanceof ModelNotFoundException) {
            return ResponseHandler::notFountException($exception, "No a record found!");
        }

        if ($exception instanceof FatalThrowableError){
            return ResponseHandler::internalServerError($exception);
        }
        
        if ($exception instanceof NotFoundHttpException ||
            $exception instanceof MethodNotAllowedHttpException) 
        {
            return ResponseHandler::notFountException($exception, "Invalid Request Url");
        }

        if($exception instanceof AuthenticationException){
            return ResponseHandler::unauth($exception);
        }
        if($exception instanceof UnauthorizedException){
            return ResponseHandler::unauth($exception);
        }

        if($exception instanceof CustomException){
            return ResponseHandler::customException($exception);
        }

        return ResponseHandler::internalServerError($exception, "Oop! There are something went wrong. Try it again or report to your Admin.");

        return parent::render($request, $exception);//this is default return handler
    }
}

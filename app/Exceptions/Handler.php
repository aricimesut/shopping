<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use ErrorException;
use Exception;
use FastRoute\BadRouteException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use JsonException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use TypeError;

class Handler extends ExceptionHandler
{

    use ApiResponser;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        return match (true) {
            $e instanceof HttpException => $this->httpException($e),
            $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
            $e instanceof ModelNotFoundException => $this->modelNotFound($e),
            $e instanceof AuthenticationException => $this->unauthenticated($e, $request),
            $e instanceof AuthorizationException => $this->unauthorized($e),
            $e instanceof BadRouteException => $this->badRoute($e),
            $e instanceof ClientException => $this->clientException($e),
            $e instanceof ServerException => $this->serverException($e),
            $e instanceof ConnectException => $this->connectException($e),
            $e instanceof InvalidArgumentException => $this->invalidArgumentException($e),
            $e instanceof TypeError => $this->typeError($e),
            $e instanceof ErrorException => $this->errorException($e),
            default => $this->genericException($e),
        };

    }

    /**
     * @param ValidationException $e
     * @param Request $request
     * @return JsonResponse
     */
    private function convertValidationExceptionToResponse(ValidationException $e, Request $request): JsonResponse
    {
        $errors = $e->validator->errors()->getMessages();
        //make array to string
        foreach ($errors as $key => $value) {
            $messageKey = array_key_first($value);
            $errors[$key] = is_numeric($messageKey) ? $value[$messageKey] : "{$messageKey} : {$value[$messageKey]}";
        }

        try {
            $errors = implode(', ', $errors);
        } catch (Throwable $e) {
            $errors = $errors[array_key_first($errors)];
        }


        return $this->response($errors, ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param ModelNotFoundException $e
     * @return JsonResponse
     */
    private function modelNotFound(ModelNotFoundException $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_NOT_FOUND);
    }

    /**
     * @param AuthenticationException $e
     * @param Request $request
     * @return JsonResponse
     */
    private function unauthenticated(AuthenticationException $e, Request $request): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_UNAUTHORIZED);
    }

    /**
     * @param AuthorizationException $e
     * @return JsonResponse
     */
    private function unauthorized(AuthorizationException $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
    }

    /**
     * @param BadRouteException $e
     * @return JsonResponse
     */
    private function badRoute(BadRouteException $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_NOT_FOUND);
    }

    /**
     * @param ClientException $e
     * @return JsonResponse
     * @throws JsonException
     */
    private function clientException(ClientException $e): JsonResponse
    {
        $content = json_decode($e->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $message = match (true) {
            isset($content['message']) => $content['message'],
            isset($content['error']) => $content['error'],
            default => $content,
        };

        return $this->response($message, (int)$e->getCode(), []);
    }

    /**
     * @param ServerException $e
     * @return JsonResponse
     */
    private function serverException(ServerException $e): JsonResponse
    {
        $content = $e->getResponse()->getBody()->getContents();

        return $this->response(json_decode($content)->message ?? $content, $e->getCode());
    }

    /**
     * @param ConnectException $e
     * @return JsonResponse
     */
    private function connectException(ConnectException $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_REQUEST_TIMEOUT);
    }

    /**
     * @param Throwable|InvalidArgumentException $e
     * @return JsonResponse
     */
    private function invalidArgumentException(Throwable|InvalidArgumentException $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
    }

    /**
     * @param TypeError $e
     * @return JsonResponse
     */
    private function typeError(TypeError $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @param Throwable $e
     * @return JsonResponse
     */
    private function genericException(Throwable $e): JsonResponse
    {
        return $this->response('Unexpected error. Try later', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param HttpException $e
     * @return JsonResponse
     */
    private function httpException(HttpException $e): JsonResponse
    {
        return $this->response(Response::$statusTexts[$e->getStatusCode()], $e->getStatusCode());
    }

    /**
     * @param ErrorException $e
     * @return JsonResponse
     */
    private function errorException(ErrorException $e): JsonResponse
    {
        return $this->response($e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
    }

}

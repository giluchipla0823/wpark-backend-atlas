<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Helpers\ApiHelper;
use App\Traits\ApiResponse;
use App\Helpers\ValidationHelper;
use Illuminate\Http\JsonResponse;
use App\Helpers\FordSt8ApiHelper;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\FORD\FordStandardErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return JsonResponse|\Illuminate\Http\Response|Response
     */
    public function render($request, Throwable $e)
    {
        if (config('app.debug')) {
            dd($e);
        }

        $extras = $this->getAdditionalExtrasToResponse($e);

        try {
            if ($e instanceof FordStandardErrorException) {
                return $this->convertFordStandardErrorExceptionToResponse($e);
            }

            if ($e instanceof ValidationException) {
                return $this->convertValidationExceptionToResponse($e, $request);
            }

            if ($e instanceof AuthenticationException) {
                return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }

            if ($e instanceof AuthorizationException) {
                return $this->errorResponse('Unauthorized.', Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof ModelNotFoundException) {
                $modelName = strtolower(class_basename($e->getModel()));

                return $this->errorResponse(
                    sprintf("There is no instance of %s with the specified id.", $modelName),
                    Response::HTTP_NOT_FOUND
                );
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse(
                    'The HTTP method specified in the request is invalid.',
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->errorResponse('The specified URL was not found.', Response::HTTP_NOT_FOUND);
            }

            if($e instanceof QueryException){
                return $this->errorResponse(
                    'Unexpected failure - 0002. Try again later.',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    $extras
                );
            }

            if ($e instanceof HttpException) {
                return $this->errorResponse($e->getMessage(), $e->getStatusCode(), $extras);
            }

            if ($e instanceof Exception) {
                return $this->errorResponse(
                    $e->getMessage(),
                    $e->getCode(),
                    array_merge($extras, method_exists($e, 'getExtras') ? $e->getExtras() : [])
                );
            }

            return $this->errorResponse(
                method_exists($e, 'getMessage') ? $e->getMessage() : 'Unexpected failure - 0001. Try again later.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $extras
            );
        } catch (Exception $exc) {
            return $this->errorResponse(
                'Unexpected failure - 0001. Try again later.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $extras
            );
        }
    }

    /**
     * @param ValidationException $e
     * @param $request
     * @return JsonResponse
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request): JsonResponse
    {
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFrontend($request)) {
            return redirect()->back()->withInput(
                $request->input()
            )->withErrors($errors);
        }

        $errors = ValidationHelper::formatErrors($errors);

        return $this->errorResponse(
            'Validation failed.',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            [ApiHelper::IDX_STR_JSON_ERRORS => $errors]
        );
    }

    /**
     * @param FordStandardErrorException $exception
     * @return JsonResponse
     * @throws Exception
     */
    private function convertFordStandardErrorExceptionToResponse(FordStandardErrorException $exception): JsonResponse
    {
        $response = FordSt8ApiHelper::transformToArrayFromSelfException($exception);

        return response()->json($response, $exception->getStatusCode());
    }

    /**
     * @param $request
     * @return bool
     */
    private function isFrontend($request): bool
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

    /**
     * @param Throwable $e
     * @return array
     */
    private function getAdditionalExtrasToResponse(Throwable $e): array
    {
        return config('app.environment') === 'local' ? [
            'exception' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]
        ] : [];
    }
}

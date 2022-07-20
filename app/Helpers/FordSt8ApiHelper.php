<?php

namespace App\Helpers;

use App\Exceptions\FORD\FordStandardErrorException;
use Exception;
use Carbon\Carbon;
use App\Models\Transport;
use Symfony\Component\HttpFoundation\Response;

class FordSt8ApiHelper
{
    public const TRANSPORT_TYPE_TRUCK = 'Truck';
    public const TRANSPORT_TYPE_RAIL = 'Rail';

    /**
     * @param array $messages
     * @param int $statusCode
     * @param string $errorCode
     * @param array $dataErrors
     * @return array
     * @throws Exception
     */
    public static function standardErrorResponse(
        array $messages,
        int $statusCode = Response::HTTP_NOT_FOUND,
        string $errorCode = "NOT_FOUND",
        array $dataErrors = []
    ): array
    {
        $referenceId = bin2hex(random_bytes(8));
        $timestamp = Carbon::now()->timestamp;

        $error = [
            "errorCode" => $errorCode,
            "messages" => $messages,
        ];

        if (count($dataErrors) > 0) {
            $error["dataErrors"] = $dataErrors;
        }

        $error["attributes"] = [
            "referenceId" => $referenceId,
            "timestamp" => $timestamp
        ];

        return [
            "type" => "https://apiguide.form.com/metadata/ford-standard-error",
            "title" => "This error is detailed using Ford's standard error format.",
            "status" => $statusCode,
            "error" => $error
        ];
    }

    /**
     * @param FordStandardErrorException $exception
     * @return array
     * @throws Exception
     */
    public static function transformToArrayFromSelfException(FordStandardErrorException $exception): array
    {
        $messages = $exception->getMessages();
        $statusCode = $exception->getStatusCode();
        $errorCode = $exception->getErrorCode();
        $dataErrors = $exception->getDataErrors();

        return self::standardErrorResponse($messages, $statusCode, $errorCode, $dataErrors);
    }

    /**
     * @return array
     */
    public static function getAllowedTransportType(): array
    {
        return [
            self::TRANSPORT_TYPE_TRUCK,
            self::TRANSPORT_TYPE_RAIL
        ];
    }

    /**
     * @param int $transportId
     * @return string
     */
    public static function getTransportType(int $transportId): string
    {
        return $transportId === Transport::TRANSPORT_TRAIN_ID
            ? self::TRANSPORT_TYPE_RAIL
            : self::TRANSPORT_TYPE_TRUCK;
    }
}

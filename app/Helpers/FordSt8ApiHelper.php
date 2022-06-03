<?php

namespace App\Helpers;

use Carbon\Carbon;

class FordSt8ApiHelper
{
    public static function getStandardErrorFormat()
    {
        $referenceId = bin2hex(random_bytes(8));

        $error_data = [
            "type" => "https://apiguide.form.com/metadata/ford-standard-error",
            "title" => "This error is detailed using Ford's standard error format.",
            "status" => 404,
            "error" => [
                "errorCode" => "NOT_FOUND",
                "messages" => [],
                "attributes" => [
                    "referenceId" => $referenceId,
                    "timestamp" => Carbon::now()->timestamp

                ]
            ]
        ];

        return $error_data;
    }
}

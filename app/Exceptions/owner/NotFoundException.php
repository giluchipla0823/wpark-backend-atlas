<?php

namespace App\Exceptions\owner;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends BaseOwnerException
{
    public function __construct(string $message = "", array $extras = [])
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $extras);
    }
}

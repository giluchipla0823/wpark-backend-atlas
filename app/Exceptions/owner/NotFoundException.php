<?php

namespace App\Exceptions\owner;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    private $extras;

    public function __construct($message = "", array $extras = [])
    {
        $this->setExtras($extras);
        parent::__construct($message, Response::HTTP_NOT_FOUND, null);
    }

    public function getExtras(): array
    {
        return $this->extras;
    }

    public function setExtras(array $extras): void
    {
        $this->extras = $extras;
    }
}

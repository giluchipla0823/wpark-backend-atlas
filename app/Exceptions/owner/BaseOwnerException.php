<?php

namespace App\Exceptions\owner;

use Exception;
use Throwable;

class BaseOwnerException extends Exception
{

    /**
     * @var array
     */
    private $extras;

    public function __construct(string $message, int $code, array $extras, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->setExtras($extras);
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

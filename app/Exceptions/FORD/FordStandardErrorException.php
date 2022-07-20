<?php

namespace App\Exceptions\FORD;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class FordStandardErrorException extends Exception
{
    public const ERROR_CODE_NOT_FOUND = 'NOT_FOUND';
    public const ERROR_CODE_VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const ERROR_CODE_UNEXPECTED_ERROR = 'UNEXPECTED_ERROR';
    /**
     * @var array
     */
    private $messages;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $dataErrors;

    public function __construct(
        array $messages,
        int $statusCode,
        array $dataErrors = []
    ){
        parent::__construct('Ford error message', $statusCode);

        $this->setMessages($messages);
        $this->setStatusCode($statusCode);
        $this->setDataErrors($dataErrors);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     * @return void
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        switch ($this->statusCode) {
            case Response::HTTP_NOT_FOUND:
                $errorCode = self::ERROR_CODE_NOT_FOUND;
                break;

            case Response::HTTP_BAD_REQUEST:
                $errorCode = self::ERROR_CODE_VALIDATION_ERROR;
                break;

            default:
                $errorCode = self::ERROR_CODE_UNEXPECTED_ERROR;
                break;
        }

        return $errorCode;
    }

    /**
     * @return array
     */
    public function getDataErrors(): array
    {
        return $this->dataErrors;
    }

    /**
     * @param array $dataErrors
     * @return void
     */
    public function setDataErrors(array $dataErrors): void
    {
        $this->dataErrors = $dataErrors;
    }
}

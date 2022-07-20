<?php

namespace App\Exceptions\owner;

use App\Exceptions\FORD\FordStandardErrorException;
use App\Helpers\FordSt8ApiHelper;
use Exception;

class TransportST8Exception extends BaseOwnerException
{
    /**
     * @var FordStandardErrorException
     */
    private $fordStandardErrorException;

    public function __construct(string $message, int $code, FordStandardErrorException $fordStandardErrorException)
    {
        parent::__construct($message, $code, []);

        $this->fordStandardErrorException = $fordStandardErrorException;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getExtras(): array
    {
        return [
            'error_details' => FordSt8ApiHelper::transformToArrayFromSelfException($this->fordStandardErrorException)
        ];
    }

}

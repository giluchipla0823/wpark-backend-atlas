<?php

namespace App\Services\External\FreightVerify;

class FreightVerifyMilestone
{
    public const VEHICLE_RECEIVED = 1;
    public const INSPECTION_COMPLETE = 2;
    public const RELEASED_TO_CARRIER = 3;
    public const COMPOUND_EXIT = 4;

    private $code;

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return match($this->code)
        {
            self::VEHICLE_RECEIVED => 'Vehicle Received',
            self::INSPECTION_COMPLETE => 'Inspection Complete',
            self::RELEASED_TO_CARRIER => 'Release to carrier',
            self::COMPOUND_EXIT => 'Compound Exit',
        };
    }

    public function getCode(): string
    {
      return match($this->code)
      {
          self::VEHICLE_RECEIVED => 'R1',
          self::INSPECTION_COMPLETE => 'XB',
          self::RELEASED_TO_CARRIER => 'J1',
          self::COMPOUND_EXIT => 'OA',
      };
    }

    public function getVmacsCode(): string
    {
      return match($this->code)
      {
          self::VEHICLE_RECEIVED => 711,
          self::INSPECTION_COMPLETE => 810,
          self::RELEASED_TO_CARRIER => 805,
          self::COMPOUND_EXIT => 721,
      };
    }
}

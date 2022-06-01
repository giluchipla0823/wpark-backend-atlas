<?php

namespace App\Services\External\FreightVerify;

enum FreightVerifyMilestone
{
    case VEHICLE_RECEIVED;
    case INSPECTION_COMPLETE;
    case RELEASED_TO_CARRIER;
    case COMPOUND_EXIT;

    public function getCode(): string
    {
      return match($this)
      {
          FreightVerifyMilestone::VEHICLE_RECEIVED => 'R1',
          FreightVerifyMilestone::INSPECTION_COMPLETE => 'XB',
          FreightVerifyMilestone::RELEASED_TO_CARRIER => 'J1',
          FreightVerifyMilestone::COMPOUND_EXIT => 'OA',
      };
    }

    public function getVmacsCode(): string
    {
      return match($this)
      {
          FreightVerifyMilestone::VEHICLE_RECEIVED => 711,
          FreightVerifyMilestone::INSPECTION_COMPLETE => 810,
          FreightVerifyMilestone::RELEASED_TO_CARRIER => 805,
          FreightVerifyMilestone::COMPOUND_EXIT => 721,
      };
    }
}
<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    public const REFERENCE_CODE_ST7 = "ST7-API";
    public const REFERENCE_CODE_ST8 = "ST8-API";
    public const REFERENCE_CODE_RECIRCULATIONS = "RECIRCULATION-WEBSERVICE";
    public const REFERENCE_CODE_FREIGHT_VERIFY = "FREIGHT-VERIFY-API";

    protected $fillable = [
        "log_name",
        "description",
        "reference_code",
        "subject_type",
        "event",
        "subject_id",
        "causer_type",
        "causer_id",
        "properties",
        "batch_uuid"
    ];
}

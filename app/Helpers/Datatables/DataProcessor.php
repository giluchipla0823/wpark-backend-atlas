<?php

namespace App\Helpers\Datatables;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Processors\DataProcessor as YajraDataProcessor;

class DataProcessor extends YajraDataProcessor
{
    /**
     * @param bool $transform
     * @return array
     */
    public function processing(bool $transform = false): array
    {
        $this->output = [];

        foreach ($this->results as $row) {
            /* @var Model $row */
            $this->output[] = $row;
        }

        return $this->output;
    }
}

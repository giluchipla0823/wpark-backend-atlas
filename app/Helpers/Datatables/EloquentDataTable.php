<?php

namespace App\Helpers\Datatables;

use Yajra\DataTables\EloquentDataTable as YajraEloquentDataTable;
use Yajra\DataTables\Exceptions\Exception as EloquentException;

class EloquentDataTable extends YajraEloquentDataTable
{

    /**
     * @param bool $transform
     * @return array
     * @throws EloquentException
     */
    public function response(bool $transform = false): array
    {
        try {
            $this->prepareQuery();

            $results = $this->results();

            $processed = $this->processingResults($results, $transform);

            return $this->render($processed);
        } catch (EloquentException $exception) {
            // TODO: Crear EloquentDatatableException para manejar errores de este tipo en Handler.php
            throw $exception;
        }
    }

    /**
     * @param $results
     * @param bool $transform
     * @return array
     */
    protected function processingResults($results, bool $transform = false): array
    {
        $processor = new DataProcessor(
            $results,
            $this->getColumnsDefinition(),
            $this->templates,
            $this->request->input('start')
        );

        return $processor->processing($transform);
    }

    /**
     * Render json response.
     *
     * @param array $data
     * @return array
     */
    protected function render(array $data): array
    {
        $output = $this->attachAppends([
            'draw'            => (int) $this->request->input('draw'),
            'recordsTotal'    => $this->totalRecords,
            'recordsFiltered' => $this->filteredRecords,
            'data'            => $data,
        ]);

        if ($this->config->isDebugging()) {
            $output = $this->showDebugger($output);
        }

        foreach ($this->searchPanes as $column => $searchPane) {
            $output['searchPanes']['options'][$column] = $searchPane['options'];
        }

        return $output;
    }
}

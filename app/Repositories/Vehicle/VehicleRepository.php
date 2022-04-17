<?php

namespace App\Repositories\Vehicle;

use App\Models\Row;
use Exception;
use App\Helpers\QueryParamsHelper;
use App\Models\Design;
use App\Models\Color;
use App\Models\Country;
use App\Models\DestinationCode;
use App\Models\Vehicle;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $vehicle)
    {
        parent::__construct($vehicle);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }

    /**
     * Crear vehículo.
     *
     * @param array $params
     * @return Model
     * @throws Exception
     */
    public function create(array $params): Model
    {
        DB::beginTransaction();

        try {
            // Creación del vehículo y relación many to many con stages
            // Extracción del vin_short desde el eoc
            // TODO: Sustituir estas relaciones cuando se cree el servicio VehicleStageService
            $eoc = $params['eoc'];

            $params['vin_short'] = substr($eoc, 24, 7);

            $params['design_id'] = Design::inRandomOrder()->first()->id;

            $params['color_id'] = Color::inRandomOrder()->first()->id;

            $params['destination_code_id'] = Country::inRandomOrder()->first()->id;

            $params['country_id'] = DestinationCode::inRandomOrder()->first()->id;

            $vehicle = $this->model->create($params);
            $stage = Stage::where('short_name', $params['stage'])->first();
            $vehicle->stages()->attach($stage->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $vehicle;
    }

    /**
     * Actualizar vehículo.
     *
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
        DB::beginTransaction();

        try {
            // Actualización del vehículo y relación many to many con stages
            $vehicle = $this->model->find($id);
            $vehicle->update($params);
            $stage = Stage::where('short_name', $params['stage'])->first();
            $vehicle->stages()->attach($stage->id);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }

        return $vehicle->id;
    }

    /**
     * Borrar Vehículo.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Borrado del Vehículo y relación many to many con stages
            $vehicle = $this->model->find($id);
            $stages = $vehicle->stages()->get();
            foreach($stages as $stage){
                $vehicle->stages()->updateExistingPivot($stage->id, ['deleted_at' => Carbon::now()]);
            }
            $vehicle->delete();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * Restaurar Vehículo.
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): ?bool
    {
        DB::beginTransaction();

        try {
            // Restauración del Vehículo y relación many to many con stages
            $vehicle = $this->model->withTrashed()->findOrFail($id);
            $stages = $vehicle->stages()->get();
            foreach($stages as $stage){
                $vehicle->stages()->updateExistingPivot($stage->id, ['deleted_at' => null]);
            }
            $vehicle->restore();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();

            throw $e;
        }
        return true;
    }

    /**
     * @param Row $row
     * @return Collection
     */
    public function findAllByRow(Row $row): Collection
    {
        $query = $this->model->query()
                    ->with(['slot'])
                    ->whereHas('slot', function(Builder $q) use ($row) {
                        $q->where('row_id', '=', $row->id)
                            ->where('fill', '=', 1);
                    });

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }
}

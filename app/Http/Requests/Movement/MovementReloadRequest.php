<?php

namespace App\Http\Requests\Movement;

use App\Models\Movement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovementReloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $movementActions = implode(",",[Movement::MOVEMENT_ACTION_OK, Movement::MOVEMENT_ACTION_ESCAPE]);

        return [
            'previous_movement_id' => "required|exists:movements,id",
            'vehicle_id' => "required|exists:vehicles,id",
            'action' => "required|integer|between:{$movementActions}",
            'comments' => "nullable"
        ];
    }

}

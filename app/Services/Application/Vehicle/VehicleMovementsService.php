<?php

namespace App\Services\Application\Vehicle;

use App\Exceptions\owner\BadRequestException;
use App\Models\Color;
use App\Models\Condition;
use App\Models\DestinationCode;
use App\Models\Design;
use App\Models\Stage;
use App\Models\Rule;
use App\Models\Vehicle;
use App\Repositories\Rule\RuleRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use App\Repositories\Block\BlockRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Mockery\Undefined;

class VehicleMovementsService
{
    /**
     * @var VehicleRepositoryInterface
     */
    private $vehicleRepository;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        RuleRepositoryInterface $ruleRepository,
        BlockRepositoryInterface $blockRepository
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->ruleRepository = $ruleRepository;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param String $vin
     * @return Vehicle|null
     * @throws Exception
     */
    public function vehicleIdentify(String $vin): ?Vehicle
    {
        if (strlen($vin) > Vehicle::VIN_SHORT_MAX_LENGTH) {
            $vehicle = $this->vehicleRepository->findBy(['vin' => $vin]);
        } else {
            $vehicle = $this->vehicleRepository->findBy(['vin_short' => $vin]);
        }

        if (!$vehicle) {
            throw new Exception('No se ha encontrado ningún vehículo con ese vin/vin_short.', Response::HTTP_NOT_FOUND);
        }

        return $vehicle;
    }

    /**
     * @param Vehicle $vehicle
     * @return array
     * @throws BadRequestException
     */
    public function vehicleMatchRules(Vehicle $vehicle): array
    {
        // Comprobamos que el vehículo tenga un "stage"
        if ($vehicle->stages->count() === 0) {
            throw new BadRequestException("El vehículo no tiene asignado ninguna etapa.");
        }

        // Sacamos las características a comprobar del vehículo
        $vehicleProperties = [
            'vin' => $vehicle->id,
            'stage' => $vehicle->latestStage->first()->id,
            'design' => $vehicle->design_id,
            'destination_code' => $vehicle->destination_code_id,
            'color' => $vehicle->color_id
        ];

        // Traemos todas la reglas simples que existan para hacer la comparación
        // $rules = Rule::where('is_group', 0)->whereIn('id', [1, 2, 12, 13, 14, 15])->get();
        $rules = Rule::where('is_group', 0)->get();

        // Recorremos todas las condiciones de todas las reglas en busca de coincidencias con el vehículo
        $matches = [];

        foreach ($rules as $index => $rule) {
            $blocks = $rule->blocks;

            if (count($rule->conditions) === 0 || count($blocks) === 0) {
                continue;
            }

            $groupConditions = $rule->conditions->groupBy('pivot.conditionable_type');

            $conditions = [];

            foreach ($groupConditions as $group => $values) {
                $conditions[] = [
                    'type' => $group,
                    'name' => current(array_unique($values->pluck('name')->toArray())),
                    'values' => $values->pluck('pivot.conditionable_id')->toArray()
                ];
            }

            $matchConditions = [];

            foreach ($conditions as $condition) {
                $type = $condition['type'];
                $values = $condition['values'];

                switch ($type) {
                    case Vehicle::class:
                        if (in_array($vehicleProperties['vin'], $values)) {
                            $matchConditions['vin'] = $vehicleProperties['vin'];
                        }
                        break;

                    case Stage::class:
                        if (in_array($vehicleProperties['stage'], $values)) {
                            $matchConditions['stage'] = $vehicleProperties['stage'];
                        }
                        break;

                    case Design::class:
                        if (in_array($vehicleProperties['design'], $values)) {
                            $matchConditions['design'] = $vehicleProperties['design'];
                        }
                        break;


                    case DestinationCode::class:
                        if (in_array($vehicleProperties['destination_code'], $values)) {
                            $matchConditions['destination_code'] = $vehicleProperties['destination_code'];
                        }
                        break;

                    case Color::class:
                        if (in_array($vehicleProperties['color'], $values)) {
                            $matchConditions['color'] = $vehicleProperties['color'];
                        }
                        break;
                }
            }

            $countConditions = count(array_keys($matchConditions));

            if ($countConditions === 0) {
                continue;
            }

            $priority = 0;
            $realPriority = 0;

            foreach ($matchConditions as $key => $matchCondition) {
                $realPriority += Condition::$priorityRuleConditions[$key];
            }

            switch ($countConditions) {
                case 1:
                    if (in_array('vin', array_keys($matchConditions))) {
                        $priority = 1;
                    } else {
                        $property = current(array_keys($matchConditions));
                        $priority += Condition::$priorityRuleConditions[$property] + 20;
                    }

                    break;
                case 5:
                    $priority = 2;
                    break;
                case 4:
                    $priority = 3;
                    break;
                case 3:
                    $priority = 4;
                    break;
                case 2:
                    $priority = 5;
                    break;
            }

            $matches[$index] = [
                'conditions' => $matchConditions,
                'count_conditions' => $countConditions,
                'priority' => $priority,
                'real_priority' => $realPriority,
                'rule' => $rule->id,
            ];

            // Añadimos a cada array los bloques asociados a la regla para posteriormente comprobar cuales son de presorting o no
            foreach ($blocks as $block) {
                $matches[$index]['blocks'][] = $block->pivot->block_id;
            }
        }

        // Ordenamos el array con los matches para que muestre por orden de prioridad
        $matches = collect($matches)->sortBy('priority')->groupBy('count_conditions');



        $doMatches = [];

        foreach ($matches as $values) {
            $values = $values->sortBy('real_priority')->values()->toArray();

            foreach ($values as $value) {
                $doMatches[] = $value;
            }
        }

        dd($doMatches);


        $last_rule_id = null;
        $shipping_rule_id = null;

        /**
         * Recorremos el array $matches y sacaremos el primer array que encontremos con block de presorting y el
         * primero que no sea de presorting, si el primer array contiene ambos el last_rule_id y el shipping_rule_id
         * estarán relacionados con la misma regla.
         */
        foreach ($doMatches as $match) {
            if (!$last_rule_id || !$shipping_rule_id) {

                foreach ($match['blocks'] as $block) {
                    $presorting = $this->blockRepository->findBy(['id' => $block, 'is_presorting' => 1]);

                    if ($presorting && !$last_rule_id) {
                        $last_rule_id = $match['rule'];
                    }

                    if (!$presorting && !$shipping_rule_id) {
                        $shipping_rule_id = $match['rule'];
                    }
                }
            }
        }

        /**
         * Por último, comprobar si las reglas seleccionadas para last_rule_id y shipping_rule_id pertenecen
         * a una regla agrupada, de ser así cambiar el id de la regla simple por el id de la regla agrupada
         * a la que pertenece.
         */
        $rulesGroup = Rule::where('is_group', 1)->get();

        foreach ($rulesGroup as $rule) {
            $isInGroup = $rule->rules_groups->contains($last_rule_id);
            if ($isInGroup) {
                $last_rule_id = $rule->id;
            }
        }

        if (!$last_rule_id) {
            throw new BadRequestException("No se encontró una regla de presorting para el vehículo.");
        }

        if (!$shipping_rule_id) {
            throw new BadRequestException("No se encontró una regla de posición final de transporte para el vehículo.");
        }

        $result = [
            'last_rule_id' => $last_rule_id,
            'shipping_rule_id' => $shipping_rule_id
        ];

        dd($result);

        // Actualización del vehículo para insertar las reglas encontradas
        $this->vehicleRepository->update($result, $vehicle->id);

        return $result;
    }

//    /**
//     * @param Vehicle $vehicle
//     * @return array
//     * @throws BadRequestException
//     */
//    public function vehicleMatchRules(Vehicle $vehicle): array
//    {
//        // Comprobamos que el vehículo tenga un "stage"
//        if ($vehicle->stages->count() === 0) {
//            throw new BadRequestException("El vehículo no tiene asignado ninguna etapa.");
//        }
//
//        // Sacamos las características a comprobar del vehículo
//        $vehicleProperties = [
//            'vin' => $vehicle->id,
//            'stage' => $vehicle->latestStage->first()->id,
//            'design' => $vehicle->design_id,
//            'destination_code' => $vehicle->destination_code_id,
//            'color' => $vehicle->color_id
//        ];
//
//        // Traemos todas la reglas simples que existan para hacer la comparación
//        $rules = Rule::where('is_group', 0)->get();
//
//        // Recorremos todas las condiciones de todas las reglas en busca de coincidencias con el vehículo
//        $matches = [];
//
//        foreach ($rules as $index => $rule) {
//
//            $conditions = $rule->conditions;
//
//            $getRule = null;
//            $rulePass = false;
//
//            // Al recorrer cada condición se comprobará si existen coincidencias con el vehículo y de ser así lo agrega al array $matches
//            foreach ($conditions as $condition) {
//
//                if ($condition->pivot->conditionable_type === Vehicle::class) {
//                    if ($vehicleProperties['vin'] === $condition->pivot->conditionable_id) {
//                        $getRule = $condition->pivot->rule_id;
//                        $getCondition = $condition->pivot->condition_id;
//                        $matches[$index]['vin'] = $getCondition;
//                    }
//                }
//
//                if ($condition->pivot->conditionable_type === Stage::class) {
//                    if ($vehicleProperties['stage'] === $condition->pivot->conditionable_id) {
//                        $getRule = $condition->pivot->rule_id;
//                        $getCondition = $condition->pivot->condition_id;
//                        $matches[$index]['stage'] = $getCondition;
//                    }
//                }
//
//                if ($condition->pivot->conditionable_type === Design::class) {
//                    if ($vehicleProperties['design'] === $condition->pivot->conditionable_id) {
//                        $getRule = $condition->pivot->rule_id;
//                        $getCondition = $condition->pivot->condition_id;
//                        $matches[$index]['design'] = $getCondition;
//                    }
//                }
//
//                if ($condition->pivot->conditionable_type === DestinationCode::class) {
//                    if ($vehicleProperties['destination_code'] === $condition->pivot->conditionable_id) {
//                        $getRule = $condition->pivot->rule_id;
//                        $getCondition = $condition->pivot->condition_id;
//                        $matches[$index]['destination_code'] = $getCondition;
//                    }
//                }
//
//                if ($condition->pivot->conditionable_type === Color::class) {
//                    if ($vehicleProperties['color'] === $condition->pivot->conditionable_id) {
//                        $getRule = $condition->pivot->rule_id;
//                        $getCondition = $condition->pivot->condition_id;
//                        $matches[$index]['color'] = $getCondition;
//                    }
//                }
//            }
//
//            // Añadimos el id de la regla a cada array que se va agregando al array $matches
//            if ($getRule) {
//                $matches[$index]['rule'] = $getRule;
//                $rulePass = true;
//            }
//
//            // Si se han encontrado coincidencias con alguna regla se empiezan a establecer las prioridades
//            if ($rulePass) {
//                $numberOfConditions = count($matches[$index]) - 1;
//                switch ($numberOfConditions) {
//                    case Rule::ONE_CONDITION:
//                        if (isset($matches[$index]['vin'])) {
//                            $matches[$index]['priority'] = 1;
//                        } else if (isset($matches[$index]['stage'])) {
//                            $matches[$index]['priority'] = 14;
//                        } else if (isset($matches[$index]['design'])) {
//                            $matches[$index]['priority'] = 15;
//                        } else if (isset($matches[$index]['destination_code'])) {
//                            $matches[$index]['priority'] = 16;
//                        } else if (isset($matches[$index]['color'])) {
//                            $matches[$index]['priority'] = 17;
//                        }
//                        break;
//                    case Rule::TWO_CONDITIONS:
//                        if (isset($matches[$index]['stage'])) {
//                            $matches[$index]['priority'] = 10;
//                        } else if (isset($matches[$index]['design'])) {
//                            $matches[$index]['priority'] = 11;
//                        } else if (isset($matches[$index]['destination_code'])) {
//                            $matches[$index]['priority'] = 12;
//                        } else if (isset($matches[$index]['color'])) {
//                            $matches[$index]['priority'] = 13;
//                        }
//                        break;
//                    case Rule::THREE_CONDITIONS:
//                        if (isset($matches[$index]['stage'])) {
//                            $matches[$index]['priority'] = 6;
//                        } else if (isset($matches[$index]['design'])) {
//                            $matches[$index]['priority'] = 7;
//                        } else if (isset($matches[$index]['destination_code'])) {
//                            $matches[$index]['priority'] = 8;
//                        } else if (isset($matches[$index]['color'])) {
//                            $matches[$index]['priority'] = 9;
//                        }
//                        break;
//                    case Rule::FOUR_CONDITIONS:
//                        if (isset($matches[$index]['stage'])) {
//                            $matches[$index]['priority'] = 2;
//                        } else if (isset($matches[$index]['design'])) {
//                            $matches[$index]['priority'] = 3;
//                        } else if (isset($matches[$index]['destination_code'])) {
//                            $matches[$index]['priority'] = 4;
//                        } else if (isset($matches[$index]['color'])) {
//                            $matches[$index]['priority'] = 5;
//                        }
//                        break;
//                    default:
//                        $matches[$index]['priority'] = 99;
//                        break;
//                }
//
//                // Añadimos a cada array los bloques asociados a la regla para posteriormente comprobar cuales son de presorting o no
//                $blocks = $rule->blocks;
//                foreach ($blocks as $block) {
//                    $matches[$index]['blocks'][] = $block->pivot->block_id;
//                }
//            }
//        }
//
//
//
//        // Ordenamos el array con los matches para que muestre por orden de prioridad
//        usort($matches, function ($a, $b) {
//            return $a['priority'] <=> $b['priority'];
//        });
//
//
//        $last_rule_id = null;
//        $shipping_rule_id = null;
//
//        /**
//         * Recorremos el array $matches y sacaremos el primer array que encontremos con block de presorting y el
//         * primero que no sea de presorting, si el primer array contiene ambos el last_rule_id y el shipping_rule_id
//         * estarán relacionados con la misma regla.
//         */
//        foreach ($matches as $match) {
//            if (!$last_rule_id || !$shipping_rule_id) {
//
//                foreach ($match['blocks'] as $block) {
//                    $presorting = $this->blockRepository->findBy(['id' => $block, 'is_presorting' => 1]);
//
//                    if ($presorting && !$last_rule_id) {
//                        $last_rule_id = $match['rule'];
//                    }
//
//                    if (!$presorting && !$shipping_rule_id) {
//                        $shipping_rule_id = $match['rule'];
//                    }
//                }
//            }
//        }
//
//        /**
//         * Por último, comprobar si las reglas seleccionadas para last_rule_id y shipping_rule_id pertenecen
//         * a una regla agrupada, de ser así cambiar el id de la regla simple por el id de la regla agrupada
//         * a la que pertenece.
//         */
//        $rulesGroup = Rule::where('is_group', 1)->get();
//
//        foreach ($rulesGroup as $rule) {
//            $isInGroup = $rule->rules_groups->contains($last_rule_id);
//            if ($isInGroup) {
//                $last_rule_id = $rule->id;
//            }
//        }
//
//        if (!$last_rule_id) {
//            throw new BadRequestException("No se encontró una regla de presorting para el vehículo.");
//        }
//
//        if (!$shipping_rule_id) {
//            throw new BadRequestException("No se encontró una regla de posición final de transporte para el vehículo.");
//        }
//
//        $result = [
//            'last_rule_id' => $last_rule_id,
//            'shipping_rule_id' => $shipping_rule_id
//        ];
//
//        // Actualización del vehículo para insertar las reglas encontradas
//        $this->vehicleRepository->update($result, $vehicle->id);
//
//        return $result;
//    }
}

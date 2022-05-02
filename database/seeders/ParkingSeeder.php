<?php

namespace Database\Seeders;

use App\Models\Row;
use App\Models\Parking;
use App\Models\Slot;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parkings = [
            [
                'name' => 'SP1',
                'area_id' => 2,
                'parking_type_id' => 2,
                'capacity' => 16,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'SP2',
                'area_id' => 2,
                'parking_type_id' => 2,
                'capacity' => 16,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'SP3',
                'area_id' => 2,
                'parking_type_id' => 2,
                'capacity' => 16,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'SP4',
                'area_id' => 2,
                'parking_type_id' => 2,
                'capacity' => 16,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'P1',
                'area_id' => 2,
                'parking_type_id' => 1,
                'capacity' => 96,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'P2',
                'area_id' => 2,
                'parking_type_id' => 1,
                'capacity' => 48,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'P3',
                'area_id' => 2,
                'parking_type_id' => 1,
                'capacity' => 40,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'FACTORY',
                'area_id' => 1,
                'parking_type_id' => 3,
                'capacity' => null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Parking::insert($parkings);

        // Creación de filas y slots en los parkings de espiga de ese área
        $parkingsSp = Parking::all()->where('area_id', 2)->where('parking_type_id', 2)->sortBy('id');

        foreach($parkingsSp as $parkingSP){
            for($i = 1; $i <= $parkingSP->capacity; $i++){
                $row = [
                    'row_number' => $i,
                    'parking_id' => $parkingSP->id,
                    'block_id' => 1,
                    'capacity' => 1,
                    'capacitymm' => 4800*$parkingSP->capacity,
                    'alt_qr' => '017.'.$parkingSP->id.'.'.$i,
                ];
                $row = Row::create($row);

                $slot = [
                    'slot_number' => 1,
                    'row_id' => $row->id,
                    'capacity' => 1,
                    'capacitymm' => $row->capacitymm
                ];
                Slot::create($slot);
            }
        }

        // Creación de filas y slots en los parkings de fila de ese área
        $parkingsP = Parking::all()->where('area_id', 2)->where('parking_type_id', 1)->sortBy('id');

        foreach($parkingsP as $parkingP){
            $totalRows = 10;
            for($i = 1; $i <= $totalRows; $i++){
                $row = [
                    'row_number' => $i,
                    'parking_id' => $parkingP->id,
                    'block_id' => 1,
                    'capacity' => 8,
                    'capacitymm' => 4800*8,
                    'alt_qr' => $parkingP['id'].'.'.$i,
                ];
                $row = Row::create($row);

                for($j = 1; $j <= $row->capacity; $j++){
                    $slot = [
                        'slot_number' => $j,
                        'row_id' => $row->id,
                        'capacity' => 1,
                        'capacitymm' => 4800
                    ];
                    Slot::create($slot);
                }
            }
        }
    }
}

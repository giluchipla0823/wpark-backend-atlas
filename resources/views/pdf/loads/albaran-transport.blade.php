<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        /* Top-bar */
        table.top-bar {
            width: 100%;
        }

        table.top-bar tbody tr td {
            width: 50%;
            vertical-align: middle;
        }

        table.top-bar tbody tr td.logo img {
            width: 116px;
        }

        table.top-bar tbody tr td.loading-list {
            text-align: right;
        }

        table.top-bar tbody tr td.loading-list h3 {
            margin: 0;
        }

        /* Header */
        table.header {
            width: 100%;
            font-size: 12px;
        }

        table.header tbody tr td.header-one {
            width: 33.33333333333333%;
        }

        table.header tbody tr td.header-two {
            width: 38.33333333333333%;
        }

        table.header tbody tr td.header-item:first-child {
            font-weight: 700;
        }

        table.header tbody tr td.header-item:last-child {
            text-align: right;
        }

        /* Vehicle details */
        div.vehicles-list {
            margin-top: 8px;
            width: 100%;
            font-size: 12px;
        }

        div.vehicles-list table.vehicle-item {
            width: 100%;
            border: 2px solid #000;
            padding: 3px;
            margin-bottom: 3px;
        }

        div.vehicles-list table.vehicle-item .vehicle-number {
            font-weight: 600;
        }

        table.vehicle-details {
            width: 100%;
        }

        table.vehicle-details tbody tr td:first-child {
            font-weight: 600;
        }

        table.vehicle-details tbody tr td {
            line-height: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <table class="top-bar">
        <tbody>
            <tr>
                <td class="logo">
                    <img src="{{ public_path('/assets/images/logo-ford.png')  }}"/>
                </td>
                <td class="loading-list" style="font-size: 13px;">
                    <h3>Loading List</h3>
                    {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
                </td>
            </tr>
        </tbody>
    </table>
    <table class="header">
        <tbody>
            <tr>
                <td class="header-item header-one">
                    Polígono Industrial, s/n.<br/>
                    46440 Almussafes <br/>
                    FORD ESPAÑA, S.L. <br/>
                    VALENCIA <br/>
                </td>
                <td class="header-item header-two">
                    <div><strong>Carrier:</strong> {{ $load->carrier->name }}</div>
                    <div><strong>Licence plate:</strong> {{ $load->license_plate }}</div>
                    <div><strong>Total Weight:</strong> {{ $total_weight  }}</div>
                    <div><strong>Number of Vehicles:</strong> {{ $counter_vehicles }}</div>
                </td>
                <td class="header-item header-three">
                    <img src="{{ 'data:image/png;base64,' . DNS1D::getBarcodePNG($load->transport_identifier, 'C128', 1, 45, [1, 1, 1], true) }}"/>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="vehicles-list">
        @php
            $iterator = 0;
        @endphp

        @foreach($vehicles as $key => $vehicle)
            @php
                $lastConfirmedMovement = $vehicle->lastConfirmedMovement;

                $rowName = null;
                $slotNumber = null;

                if ($lastConfirmedMovement->destinationPosition && $lastConfirmedMovement->destinationPosition->row) {
                    $rowName = $lastConfirmedMovement->destinationPosition->row->row_name;
                    $slotNumber = $lastConfirmedMovement->destinationPosition->slot_number;
                }

                $iterator ++;
            @endphp

            <table class="vehicle-item">
                <tbody>
                    <tr>
                        <td colspan="2" class="vehicle-number">
                            {{ ($key + 1) }}. Vehicle information
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%">
                            <table class="vehicle-details">
                                <tbody>
                                    <tr>
                                        <td>VIN 7 digits:</td>
                                        <td>{{ $vehicle->vin_short }}</td>
                                    </tr>
                                    <tr>
                                        <td>Weight:</td>
                                        <td>{{ $vehicle->design->weight }}</td>
                                    </tr>
                                    <tr>
                                        <td>Width:</td>
                                        <td>{{ $vehicle->design->width }}</td>
                                    </tr>
                                    <tr>
                                        <td>Length:</td>
                                        <td>{{ $vehicle->design->length }}</td>
                                    </tr>

                                    <tr>
                                        <td>Height:</td>
                                        <td>{{ $vehicle->design->height }}</td>
                                    </tr>

                                    <tr>
                                        <td>Category:</td>
                                        <td>{{ $vehicle->shippingRule->name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Dest code:</td>
                                        <td>{{ $vehicle->destinationCode->code }}</td>
                                    </tr>

                                    <tr>
                                        <td>Market:</td>
                                        <td>{{ $vehicle->destinationCode->country->name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Dealer code:</td>
                                        <td>{{ $vehicle->dealer->name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Color:</td>
                                        <td>{{ $vehicle->color->name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Custom documents:</td>
                                        <td>{{ $vehicle->custom_documents ? 'SI' : 'NO' }}</td>
                                    </tr>

                                    <tr>
                                        <td>Model:</td>
                                        <td>{{ $vehicle->design->name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="vertical-align: top;width: 50%">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="text-align: center;">
                                            <strong>Position</strong>
                                            <h3 style="margin: 0; font-size: 20px">{{ $rowName }}</h3>
                                            <h3 style="margin: 0; font-size: 20px">{{ $slotNumber }}</h3>
                                        </td>
                                        <td style="text-align: right">
                                            <img src="{{ 'data:image/png;base64,' . DNS1D::getBarcodePNG($vehicle->vin, 'C128', 1.15, 45, [1,1,1], true)  }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; vertical-align: top;">
                                            <div style="margin-top: 50px;">
                                                <strong>Address:</strong>
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <div style="margin-top: 50px; margin-left: 20px;">
                                                @if($vehicle->dealer->id === 9999)
                                                    {{ $vehicle->dealer->name  }}
                                                @else
                                                    {{ $vehicle->dealer->name }} <br />
                                                    {{ $vehicle->dealer->street }} <br />
                                                    {{ $vehicle->dealer->zip_code }} {{ $vehicle->dealer->city }}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($iterator === 4 && (($counter_vehicles - 1) !== $key))
                @php
                    $iterator = 0;
                @endphp
                <div class="page-break"></div>
            @endif

        @endforeach
    </div>
</body>
</html>

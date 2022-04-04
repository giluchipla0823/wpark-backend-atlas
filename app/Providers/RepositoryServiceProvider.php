<?php

namespace App\Providers;

use App\Repositories\Color\ColorRepository;
use App\Repositories\Color\ColorRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Compound\CompoundRepository;
use App\Repositories\Compound\CompoundRepositoryInterface;
use App\Repositories\Zone\ZoneRepository;
use App\Repositories\Zone\ZoneRepositoryInterface;
use App\Repositories\Area\AreaRepository;
use App\Repositories\Area\AreaRepositoryInterface;
use App\Repositories\Parking\ParkingTypeRepository;
use App\Repositories\Parking\ParkingTypeRepositoryInterface;
use App\Repositories\Parking\ParkingRepository;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Block\BlockRepository;
use App\Repositories\Block\BlockRepositoryInterface;
use App\Repositories\Row\RowRepository;
use App\Repositories\Row\RowRepositoryInterface;
use App\Repositories\Slot\SlotRepository;
use App\Repositories\Slot\SlotRepositoryInterface;
use App\Repositories\Brand\BrandRepository;
use App\Repositories\Brand\BrandRepositoryInterface;
use App\Repositories\Design\DesignRepository;
use App\Repositories\Design\DesignRepositoryInterface;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Country\CountryRepositoryInterface;
use App\Repositories\Route\RouteRepository;
use App\Repositories\Route\RouteRepositoryInterface;
use App\Repositories\DestinationCode\DestinationCodeRepository;
use App\Repositories\DestinationCode\DestinationCodeRepositoryInterface;
use App\Repositories\Condition\ConditionRepository;
use App\Repositories\Condition\ConditionRepositoryInterface;
use App\Repositories\Hold\HoldRepository;
use App\Repositories\Hold\HoldRepositoryInterface;
use App\Repositories\Vehicle\StageRepository;
use App\Repositories\Vehicle\StageRepositoryInterface;
use App\Repositories\Vehicle\VehicleRepository;
use App\Repositories\Vehicle\VehicleRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            ColorRepositoryInterface::class,
            ColorRepository::class
        );

        $this->app->bind(
            CompoundRepositoryInterface::class,
            CompoundRepository::class
        );

        $this->app->bind(
            ZoneRepositoryInterface::class,
            ZoneRepository::class
        );

        $this->app->bind(
            AreaRepositoryInterface::class,
            AreaRepository::class
        );

        $this->app->bind(
            ParkingTypeRepositoryInterface::class,
            ParkingTypeRepository::class
        );

        $this->app->bind(
            ParkingRepositoryInterface::class,
            ParkingRepository::class
        );

        $this->app->bind(
            BlockRepositoryInterface::class,
            BlockRepository::class
        );

        $this->app->bind(
            RowRepositoryInterface::class,
            RowRepository::class
        );

        $this->app->bind(
            SlotRepositoryInterface::class,
            SlotRepository::class
        );

        $this->app->bind(
            BrandRepositoryInterface::class,
            BrandRepository::class
        );

        $this->app->bind(
            DesignRepositoryInterface::class,
            DesignRepository::class
        );

        $this->app->bind(
            CountryRepositoryInterface::class,
            CountryRepository::class
        );

        $this->app->bind(
            RouteRepositoryInterface::class,
            RouteRepository::class
        );

        $this->app->bind(
            DestinationCodeRepositoryInterface::class,
            DestinationCodeRepository::class
        );

        $this->app->bind(
            ConditionRepositoryInterface::class,
            ConditionRepository::class
        );

        $this->app->bind(
            HoldRepositoryInterface::class,
            HoldRepository::class
        );

        $this->app->bind(
            StageRepositoryInterface::class,
            StageRepository::class
        );

        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );
    }
}

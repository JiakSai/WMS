<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\View;
use App\Models\Sys\Modu\SysModuMains;
use App\Models\Sys\Modu\SysModuSubms;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blueprint::macro('commonFields', function () {
            $this->unsignedBigInteger('organization_id')->nullable()->comment('Organization ID');
            $this->timestamp('created_at')->nullable()->comment('Created Time');
            $this->string('created_by',200)->nullable()->comment('Created By');
            $this->timestamp('updated_at')->nullable()->comment('Updated Time');
            $this->string('updated_by',200)->nullable()->comment('Updated By');
            $this->softDeletes()->nullable()->comment('Soft Delete');
        });

        View::composer('components.slidebar', function ($view) {

            $mainModules = SysModuMains::where ('is_active', 1)
                                        ->get();

            $subModules = SysModuSubms::get();

            $view->with('mainModules', $mainModules)
                ->with('subModules', $subModules);
        });
    }
}

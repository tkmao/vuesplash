<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * app()->isLocal() // 実行環境がlocalかどうかをチェックしている
         * .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
         */
        if (app()->isLocal() || app()->runningUnitTests()) {
            // Faker 日本語
            $this->app->singleton(\Faker\Generator::class, function () {
                return \Faker\Factory::create('ja_JP');
            });
        }

        $bindData = [
            // Service
            'App\Services\User\CompanyServiceInterface'                 => 'App\Services\User\CompanyService',
            'App\Services\User\HolidayServiceInterface'                 => 'App\Services\User\HolidayService',
            'App\Services\User\ProjectServiceInterface'                 => 'App\Services\User\ProjectService',
            'App\Services\User\UserContractServiceInterface'            => 'App\Services\User\UserContractService',
            'App\Services\User\UserServiceInterface'                    => 'App\Services\User\UserService',
            'App\Services\User\UserTypeServiceInterface'                => 'App\Services\User\UserTypeService',
            'App\Services\User\WeeklyReportServiceInterface'            => 'App\Services\User\WeeklyReportService',
            'App\Services\User\WorkScheduleMonthServiceInterface'       => 'App\Services\User\WorkScheduleMonthService',
            'App\Services\User\WorkScheduleServiceInterface'            => 'App\Services\User\WorkScheduleService',

            // Repository
            'App\Repositories\CompanyRepositoryInterface'               => 'App\Repositories\CompanyRepository',
            'App\Repositories\HolidayRepositoryInterface'               => 'App\Repositories\HolidayRepository',
            'App\Repositories\ProjectRepositoryInterface'               => 'App\Repositories\ProjectRepository',
            'App\Repositories\ProjectWorkRepositoryInterface'           => 'App\Repositories\ProjectWorkRepository',
            'App\Repositories\UserContractRepositoryInterface'          => 'App\Repositories\UserContractRepository',
            'App\Repositories\UserRepositoryInterface'                  => 'App\Repositories\UserRepository',
            'App\Repositories\UserTypeRepositoryInterface'              => 'App\Repositories\UserTypeRepository',
            'App\Repositories\WeeklyReportRepositoryInterface'          => 'App\Repositories\WeeklyReportRepository',
            'App\Repositories\WorkScheduleMonthRepositoryInterface'     => 'App\Repositories\WorkScheduleMonthRepository',
            'App\Repositories\WorkScheduleRepositoryInterface'          => 'App\Repositories\WorkScheduleRepository',
        ];

        foreach ($bindData as $bindKey => $bindClass) {
            $this->app->bind($bindKey, $bindClass);
        }
    }
}

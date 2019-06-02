<?php

use App\Repositories\Models\WorkScheduleMonth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class WorkScheduleMonthsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('work_schedule_months')->truncate();

        $workScheduleMonths = new WorkScheduleMonth();
        $workScheduleMonths->id = 1;
        $workScheduleMonths->user_id = 35;
        $workScheduleMonths->yearmonth = 201901;
        $workScheduleMonths->is_subumited = true;
        $workScheduleMonths->save();

        $workScheduleMonths = new WorkScheduleMonth();
        $workScheduleMonths->id = 2;
        $workScheduleMonths->user_id = 35;
        $workScheduleMonths->yearmonth = 201902;
        $workScheduleMonths->is_subumited = true;
        $workScheduleMonths->save();

        $workScheduleMonths = new WorkScheduleMonth();
        $workScheduleMonths->id = 3;
        $workScheduleMonths->user_id = 35;
        $workScheduleMonths->yearmonth = 201903;
        $workScheduleMonths->is_subumited = true;
        $workScheduleMonths->save();

        $workScheduleMonths = new WorkScheduleMonth();
        $workScheduleMonths->id = 4;
        $workScheduleMonths->user_id = 35;
        $workScheduleMonths->yearmonth = 201904;
        $workScheduleMonths->is_subumited = true;
        $workScheduleMonths->save();

        $workScheduleMonths = new WorkScheduleMonth();
        $workScheduleMonths->id = 5;
        $workScheduleMonths->user_id = 35;
        $workScheduleMonths->yearmonth = 201905;
        $workScheduleMonths->is_subumited = true;
        $workScheduleMonths->save();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

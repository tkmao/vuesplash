<?php

use App\Repositories\Models\ProjectWork;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ProjectWorksTableSeeder extends Seeder
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

        DB::table('project_works')->truncate();

        $workScheduleMaxId = App\Repositories\Models\WorkSchedule::max('id');
        $worktimes = array(0, 0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.25, 2.5, 2.75, 3);

        for ($i = 1; $i <= $workScheduleMaxId; $i++) {
            for ($j = 1; $j <= 2; $j++) {
                $projectWorks = new ProjectWork();
                $projectWorks->work_schedule_id = $i;
                $projectWorks->project_id = App\Repositories\Models\Project::inRandomOrder()->value('id');
                $projectWorks->worktime = $worktimes[array_rand($worktimes, 1)];
                $projectWorks->save();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

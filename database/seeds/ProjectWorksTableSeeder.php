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

        $workSchedules = App\Repositories\Models\WorkSchedule::orderBy('id', 'asc')->get(['id','workdate']);
        $worktimes = array(0, 0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.25, 2.5, 2.75, 3);

        $previousYearMonth = 0;
        $porject_id_1 = 1;
        $porject_id_2 = 2;

        foreach ($workSchedules as $key => $workSchedule) {
            $workdatedt = new \Carbon\Carbon($workSchedule->workdate);
            $currentYearMonth = $workdatedt->copy()->format('Ym');

            if ($previousYearMonth !== $currentYearMonth) {
                $porject_id_1 = App\Repositories\Models\Project::inRandomOrder()->value('id');
                $porject_id_2 = App\Repositories\Models\Project::inRandomOrder()->value('id');
                while ($porject_id_1 == $porject_id_2) {
                    $porject_id_2 = App\Repositories\Models\Project::inRandomOrder()->value('id');
                }
                $previousYearMonth = $currentYearMonth;
            }
            
            for ($j = 1; $j <= 2; $j++) {
                $projectWorks = new ProjectWork();
                $projectWorks->work_schedule_id = $workSchedule->id;
                $projectWorks->project_id = ($j === 1) ? $porject_id_1 : $porject_id_2;
                $projectWorks->worktime = $worktimes[array_rand($worktimes, 1)];
                $projectWorks->save();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

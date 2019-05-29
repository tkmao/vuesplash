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

        $project_id = 1;
        $startWS = 121;

        for ($i = $startWS; $i <= 151; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $projectWorks = new ProjectWork();
                $projectWorks->work_schedule_id = $i;
                $projectWorks->project_id = $project_id;
                $projectWorks->worktime = $j;
                $projectWorks->save();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

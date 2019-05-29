<?php

use App\Repositories\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ProjectStatusesTableSeeder extends Seeder
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

        DB::table('project_statuses')->truncate();

        $file = new SplFileObject('database/csv/ProjectStatuses.csv');
        $file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::DROP_NEW_LINE
        );
        $list = [];
        $now = \Carbon\Carbon::now();
        foreach ($file as $line) {
            $list[] = [
                "name" => $line[0],
                "is_deleted" => false,
                "created_at" => $now,
                "updated_at" => $now,
            ];
        }

        DB::table("project_statuses")->insert($list);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

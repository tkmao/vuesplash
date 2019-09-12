<?php

use App\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
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

        DB::table('projects')->truncate();

        $appenv = config('app.env');
        if ($appenv === 'local') {
            $file = new SplFileObject('database/csv/Projects_test.csv');
        } else {
            $file = new SplFileObject('database/csv/Projects.csv');
        }
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
                "code" => $line[0],
                "name" => $line[1],
                "category_id" => App\Repositories\Models\Category::inRandomOrder()->value('id'),
                "company_id" => App\Repositories\Models\Company::inRandomOrder()->value('id'),
                "user_id" => App\Repositories\Models\User::inRandomOrder()->value('id'),
                "status_id" => App\Repositories\Models\ProjectStatus::inRandomOrder()->value('id'),
                "is_deleted" => false,
                "created_at" => $now,
                "updated_at" => $now,
            ];
        }

        DB::table("projects")->insert($list);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

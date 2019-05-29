<?php

use App\Repositories\Models\Holiday;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class HolidaysTableSeeder extends Seeder
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

        DB::table('holidays')->truncate();

        $file = new SplFileObject('database/csv/Holidays.csv');
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
                "date" => $line[0],
                "name" => $line[1],
                "created_at" => $now,
                "updated_at" => $now,
            ];
        }

        DB::table("holidays")->insert($list);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

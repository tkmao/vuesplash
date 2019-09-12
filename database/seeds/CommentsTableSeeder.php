<?php

use App\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
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

        DB::table('comments')->truncate();

        $file = new SplFileObject('database/csv/Comments.csv');
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
                "photo_id" => $line[0],
                "user_id" => $line[1],
                "content" => $line[2],
                "created_at" => $now,
                "updated_at" => $now,
            ];
        }

        DB::table("comments")->insert($list);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

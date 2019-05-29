<?php

use App\Repositories\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();   // セキュリティー解除 (Laravel は仕様上、あまり固まったデータを投入することはできないので、それを解除している)
                            // Eloquentはセキュリティーを向上させるため、Eloquentモデルのフィールドに対する複数代入に制限を付けられるようになっています。
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // 外部キー制約違反エラーを出さないようにする

        DB::table('users')->truncate();

        $file = new SplFileObject('database/csv/Users.csv');
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
                'name' => $line[0],
                'email' => $line[1],
                'password'  => bcrypt('pass'),
                'remember_token' => str_random(60),
                'api_token' => str_random(60),
                'usertype_id' => 1,
                'workingtime_type' => 1,
                'worktime_day' => 8,
                'maxworktime_month' => 20,
                'workingtime_min' => 152,
                'workingtime_max' => 200,
                'hiredate' => $line[2],
                'paid_holiday' => 10,
                'is_admin'  => false,
                'is_deleted' => false,
                "created_at" => $now,
                "updated_at" => $now,
            ];
        }

        DB::table("users")->insert($list);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // 外部キー制約違反エラーを出すように再設定
        Model::reguard();    // セキュリティー再設定
    }
}

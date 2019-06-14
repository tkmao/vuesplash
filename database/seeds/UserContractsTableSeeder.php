<?php

use App\Repositories\Models\UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class UserContractsTableSeeder extends Seeder
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

        DB::table('user_contracts')->truncate();

        $userCount = App\Repositories\Models\User::count();
        $now = \Carbon\Carbon::now();
        for ($i = 1; $i <= $userCount; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $userContracts = new UserContract();
                $userContracts->user_id = $i;
                $userContracts->usertype_id = rand(1, 5);
                $userContracts->workingtime_type = rand(1, 2);
                $userContracts->worktime_day = 8;
                $userContracts->maxworktime_month = 20;
                $userContracts->workingtime_min = rand(150, 165);
                $userContracts->workingtime_max = rand(180, 200);
                $userContracts->startdate = ($j === 0) ? '2015-09-01' : (($j === 1) ? '2019-03-01' : '2019-05-01');
                $userContracts->enddate = ($j === 0) ? '2019-02-28' : (($j === 1) ? '2019-04-30' : '2030-12-30');
                $userContracts->created_at = $now;
                $userContracts->updated_at = $now;
                $userContracts->save();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // 外部キー制約違反エラーを出すように再設定
        Model::reguard();    // セキュリティー再設定
    }
}

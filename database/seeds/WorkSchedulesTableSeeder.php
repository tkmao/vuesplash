<?php

use App\Repositories\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class WorkSchedulesTableSeeder extends Seeder
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

        DB::table('work_schedules')->truncate();

        // 配列を使用し、要素順に(日:0〜土:6)を設定する
        $weeklist = [
                '日', //0
                '月', //1
                '火', //2
                '水', //3
                '木', //4
                '金', //5
                '土', //6
            ];

        $start = '20190201'; // 開始日時
        $end = '20190630';   // 終了日時
        $today = date("Ymd"); // 今日の日付
        $userCount = DB::table('users')->count(); // 全ユーザ件数
        $minutes = array(0, 15, 30, 45);
        $breaktimes = array(0, 0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.25, 2.5, 2.75, 3);

        for ($i = 1; $i <= $userCount; $i++) {
            for ($j = $start; $j <= $end; $j = date('Ymd', strtotime($j . '+1 day'))) {
                $workSchedules = new WorkSchedule();
                $workSchedules->user_id = $i;
                $workSchedules->workdate = $j;
                $workSchedules->week_number = date('Y', strtotime($j)) . date('W', strtotime($j));
                $week = date('w', strtotime($j));
                if ($week === "0" || $week === "6") {
                    $workSchedules->detail = '休日だよ。' . 'ユーザID：' . $i . '、日付：' . $j . '（' . $weeklist[$week] . '）';
                    $workSchedules->is_paid_holiday = false;
                } else {
                    if ($today > $j) {
                        $workSchedules->starttime_hh = str_pad(rand(8, 10), 2, "0", STR_PAD_LEFT);
                        $workSchedules->starttime_mm = str_pad($minutes[array_rand($minutes, 1)], 2, "0", STR_PAD_LEFT);
                        $workSchedules->endtime_hh = str_pad(rand(16, 23), 2, "0", STR_PAD_LEFT);
                        $workSchedules->endtime_mm = str_pad($minutes[array_rand($minutes, 1)], 2, "0", STR_PAD_LEFT);
                        $workSchedules->breaktime = $breaktimes[array_rand($breaktimes, 1)];
                        $workSchedules->breaktime_midnight = 0;
                    }
                    $workSchedules->detail = '出勤だよ。' . 'ユーザID：' . $i . '、日付：' . $j . '（' . $weeklist[$week] . '）';
                    $workSchedules->is_paid_holiday = false;
                }
                $workSchedules->save();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

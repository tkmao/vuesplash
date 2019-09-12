<?php

use App\Repositories\Models\WeeklyReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class WeeklyReportsTableSeeder extends Seeder
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

        DB::table('weekly_reports')->truncate();

        /**
         * date_format('2017-01-01', '%X%V') as nichiyou_start -- 201701
         * date_format('2017-01-01', '%x%v') as getsuyou_start -- 201652
         */

        $userCount = DB::table('users')->count(); // 全ユーザ件数
        $now = \Carbon\Carbon::now();
        $weekNumber = $now->year . str_pad($now->weekOfYear, 2, 0, STR_PAD_LEFT);

        // ユーザごとにデータ登録する
        for ($i = 1; $i <= $userCount; $i++) {
            // 過去3か月の情報を入力する
            $execDate = $now->copy()->subMonth(3);
            $execWeekNumber = $execDate->year . str_pad($execDate->weekOfYear, 2, 0, STR_PAD_LEFT);

            // 週報登録
            for ($dt = $execDate; $execWeekNumber < $weekNumber; $dt->addWeek(1)) {
                $year = $dt->year;
                $week = str_pad($dt->weekOfYear, 2, 0, STR_PAD_LEFT);
                $execWeekNumber = $year . $week;

                $weeklyReports = new WeeklyReport();
                $weeklyReports->user_id = $i;
                $weeklyReports->week_number = $execWeekNumber;
                $weeklyReports->project_id = App\Repositories\Models\Project::inRandomOrder()->value('id');
                $weeklyReports->nextweek_schedule = 'ユーザID：' . $i . '、週番号：' . $execWeekNumber . '、来週の予定';
                $weeklyReports->thismonth_dayoff = 'ユーザID：' . $i . '、週番号：' . $execWeekNumber . '、今月の休み';
                $weeklyReports->site_information = 'ユーザID：' . $i . '、週番号：' . $execWeekNumber . '、現場の情報';
                $weeklyReports->opinion = 'ユーザID：' . $i . '、週番号：' . $execWeekNumber . '、所感';
                $weeklyReports->is_subumited = rand(0, 1) == 1;
                $weeklyReports->save();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

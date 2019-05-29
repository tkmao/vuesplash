<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(HolidaysTableSeeder::class);
        $this->call(ProjectStatusesTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(WorkSchedulesTableSeeder::class);
        $this->call(ProjectWorksTableSeeder::class);
        $this->call(WeeklyReportsTableSeeder::class);
        $this->call(WorkScheduleMonthsTableSeeder::class);
    }
}

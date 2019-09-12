<?php

use App\Repositories\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
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

        DB::table('companies')->truncate();

        $testCompany = [
            'id' => 1,
            'name' => '株式会社エミシス',
            'zipcode' => '8191631',
            'address' => '福岡県福岡市博多区祇園町６−４３ ギオン柴田ビル３F',
            'phone' => '092-000-0000',
            'fax' => '092-000-1111',
            'is_deleted' => false,
        ];

        factory(Company::class, 1)->create($testCompany);
        factory(Company::class, 20)->create();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}

<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Repositories\Models\Company::class, function (Faker $faker) use ($factory) {
    return [
        'name' => $faker->company,
        'zipcode' => $faker->postcode,
        'address' => $faker->address,
        'phone'  => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'is_deleted' => false,
    ];
});

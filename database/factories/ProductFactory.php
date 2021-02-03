<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use App\Product;
use Illuminate\Support\Str;
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

$factory->define(Product::class, function (Faker $faker) use ($factory) {
    return [
        'name' => $faker->name,
        // 'phone_number' =>  $faker->unique()->randomNumber(8),
        'details' => $faker->text(),
        'type' => array_random(['حالي','حالي و مسبق','مسبق']),
        'price' => 100,
        'preparation_time'  => 20,
        'category_id'  =>  $factory->create(Category::class)->id,
        'provider_id' => App\User::first()->id,
        'image' => 'default.png',
        'views'=>1,
    ];
});

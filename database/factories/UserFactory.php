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

$factory->define(App\User::class, function (Faker $faker) {

    static $number = 1;

    return [
        'id' => $number++,
        'name' => $faker->name,
        'phone' => $faker->phoneNumber,
        'email' => $faker->unique()->safeEmail,
        'profile_image' => "http://via.placeholder.com/150x150",
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Message::class, function (Faker $faker) {

    static $number = 1;

	do {
		$from = rand(1, 12);
		$to = rand(1, 12);
	} while ($from == $to);

    return [
        'id' => $number++,
        'from' => $from,
        'to' => $to,
        'text' => $faker->sentence,
    ];
});
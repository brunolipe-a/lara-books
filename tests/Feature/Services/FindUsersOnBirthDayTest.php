<?php

use App\Services\FindUsersOnBirthDayService;
use Tests\Utils\Firestore\Faker\UserFaker;

it('should be able to return only user on birthday', function () {
    UserFaker::factory()->create(['birthday' => now()->format('Y-m-d')]);
    UserFaker::factory()->create(['birthday' => now()->format('Y-m-d')]);
    UserFaker::factory()->create(['birthday' => now()->format('Y-m-d')]);

    UserFaker::factory()->createMany(2);

    $service = app(FindUsersOnBirthDayService::class);

    $users = $service->handle();

    expect($users)->toHaveCount(3);
});

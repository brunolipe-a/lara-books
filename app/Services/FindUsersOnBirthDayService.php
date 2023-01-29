<?php

namespace App\Services;

use App\Repositories\Firestore\UserRepository;
use Carbon\Carbon;

class FindUsersOnBirthDayService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function handle()
    {
        $users = collect(
            $this->userRepository
                ->query()
                ->where('is_active', '=', true)
                ->documents()
                ->rows(),
        );

        return $users->filter(fn($user) => $this->isSameDayAndMonthToday($user->data()['birthday']));
    }

    private function isSameDayAndMonthToday(string $date): bool
    {
        return Carbon::parse($date)->format('m-d') === today()->format('m-d');
    }
}

<?php

namespace App\Services;

use App\Mail\UserBirthday;
use App\Repositories\Firestore\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendBirthdayMailService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function handle($user)
    {
        $userData = $user->data();

        $userData['books_counter'] = Cache::get("user-{$user->id()}-books-counter", 0);
        $userData['pages_counter'] = Cache::get("user-{$user->id()}-pages-counter", 0);

        Mail::to($userData['email'])->send(new UserBirthday($userData));

        Cache::deleteMultiple(["user-{$user->id()}-books-counter", "user-{$user->id()}-pages-counter"]);
    }
}

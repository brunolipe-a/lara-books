<?php

namespace App\Console\Commands;

use App\Services\FindUsersOnBirthDayService;
use App\Services\SendBirthdayMailService;
use Illuminate\Console\Command;

class SendBirthdayMailCommand extends Command
{
    protected $signature = 'users:birthday-mail';

    protected $description = 'Command description';

    public function handle(
        FindUsersOnBirthDayService $findUsersOnBirthDayService,
        SendBirthdayMailService $sendBirthdayMailService,
    ) {
        $users = $findUsersOnBirthDayService->handle();

        foreach ($users as $user) {
            $this->info("Enviando para {$user->id()}");
            $sendBirthdayMailService->handle($user);
        }

        return Command::SUCCESS;
    }
}

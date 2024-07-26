<?php

namespace sipai\UserManager\Commands;

use Illuminate\Console\Command;

class UserManagerCommand extends Command
{
    public $signature = 'usermanager';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

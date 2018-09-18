<?php

namespace App\Commands\System;

use App\Manager\BotManager;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Update;

abstract class BaseCommand extends SystemCommand
{
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->private_only = true;
        $this->need_mysql = false;

        parent::__construct($botManager, $update);
    }
}

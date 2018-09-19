<?php

namespace App\Manager;

use App\Commands\System\GenericmessageCommand;
use App\Commands\System\StartCommand;
use App\Repository\UserRepository;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;

class BotManager extends Telegram
{
    /** @var UserRepository */
    protected $userRepository;
    /**
     * @param UserRepository $userRepository
     * @throws TelegramException
     * @throws TelegramLogException
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        $apiKey = getenv('API_KEY');
        $botUsername = getenv('BOT_USERNAME');

        class_alias(
            StartCommand::class,
            \Longman\TelegramBot\Commands\SystemCommands\StartCommand::class
        );

        class_alias(
            GenericmessageCommand::class,
            \Longman\TelegramBot\Commands\SystemCommands\GenericmessageCommand::class
        );

        TelegramLog::initErrorLog('php://stderr');
        TelegramLog::initDebugLog('php://stdout');
        TelegramLog::initUpdateLog('php://stdout');

        parent::__construct($apiKey, $botUsername);
    }

    /**
     * @return void
     * @throws TelegramException
     */
    public function handle(): void
    {
        $adminId = (int)getenv('ADMIN_ID');

        $this->enableAdmins([$adminId]);
        //$this->app->enableLimiter();
        $this->addCommandsPaths([
            __DIR__ . '/../Commands/System',
            __DIR__ . '/../Commands/User'
        ]);

        // Handle telegram webhook request
        parent::handle();
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }
}

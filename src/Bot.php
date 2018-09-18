<?php

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

class Bot
{
    /** @var Telegram */
    private $app;

    /**
     * Bot constructor.
     * @throws TelegramException
     */
    public function __construct()
    {
        $apiKey = getenv('API_KEY');
        $botUsername = getenv('BOT_USERNAME');

        // Create Telegram API object
        $this->app = new Telegram($apiKey, $botUsername);
    }

    /**
     * @return Telegram
     */
    public function getApp(): Telegram
    {
        return $this->app;
    }

    /**
     * @return void
     * @throws TelegramException
     */
    public function setWebHook(): void
    {
        $hookUrl = getenv('HOOK_URL');

        $result = $this->app->setWebhook($hookUrl);
        if ($result->isOk()) {
            echo $result->getDescription();
        }
    }

    /**
     * @return void
     * @throws TelegramException
     */
    public function handle(): void
    {
        $adminId = (int)getenv('ADMIN_ID');

        $this->app->enableAdmins([$adminId]);
        //$this->app->enableLimiter();
        $this->app->addCommandsPaths([
            __DIR__ . '/Commands'
        ]);

        // Handle telegram webhook request
        $this->app->handle();
    }
}

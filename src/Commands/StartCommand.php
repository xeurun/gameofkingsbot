<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Factory\ScreenFactory;
use Interfaces\ScreenInterface;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';
    /**
     * @var string
     */
    protected $description = 'Start command';
    /**
     * @var string
     */
    protected $usage = '/start';
    /**
     * @var string
     */
    protected $version = '1.1.0';
    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chatId = $message->getChat()->getId();

        $screen = null;
        $result = Request::emptyResponse();

        $screenFactory = new ScreenFactory();
        if ($screenFactory->isAvailableScreen( ScreenInterface::SCREEN_MAIN_MENU)) {
            $screen = $screenFactory->createScreen($chatId,  ScreenInterface::SCREEN_MAIN_MENU);
        }

        if (null !== $screen) {
            $result = $screen->execute();
        }

        return $result;
    }
}

<?php

namespace App\Commands\System;

use App\Factory\ScreenFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
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
    protected $version = '1.0.0';
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

        /** @var BotManager $telegram */
        $telegram = $this->getTelegram();
        $userRepository = $telegram->getUserRepository();

        $screen = null;
        $result = Request::emptyResponse();

        if (!$userRepository->find($chatId)) {
            $keyboard = new Keyboard(
                ['Начать!']
            );

            //Return a random keyboard.
            $keyboard = $keyboard
                ->setResizeKeyboard(true)
                ->setOneTimeKeyboard(false)
                ->setSelective(false);

            $text = <<<TEXT
*Приветствуем нового короля!*
TEXT;

            $data    = [
                'chat_id'      => $chatId,
                'text'         => $text,
                'reply_markup' => $keyboard,
                'parse_mode'   => 'Markdown'
            ];

            Request::sendMessage($data);

            $text = <<<TEXT
Вас зовут: Алексей I
Ваше королевство называется: Нарния 
TEXT;

            $inlineKeyboard = new InlineKeyboard([
                ['text' => 'Изменить имя', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
                ['text' => 'Изменить название', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
            ]);

            $data = [
                'chat_id'      => $chatId,
                'text'         => $text,
                'reply_markup' => $inlineKeyboard,
                'parse_mode'   => 'Markdown',
            ];

            $result = Request::sendMessage($data);
        } else {
            $screenFactory = new ScreenFactory();
            if ($screenFactory->isAvailableScreen( ScreenInterface::SCREEN_MAIN_MENU)) {
                $screen = $screenFactory->createScreen($chatId,  ScreenInterface::SCREEN_MAIN_MENU);
            }

            if (null !== $screen) {
                $result = $screen->execute();
            }
        }

        return $result;
    }
}

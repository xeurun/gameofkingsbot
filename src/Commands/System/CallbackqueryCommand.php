<?php

namespace App\Commands\System;

use App\Entity\Kingdom;
use App\Factory\CallbackFactory;
use App\Manager\BotManager;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class CallbackqueryCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'callbackquery';
        $this->description = 'Reply to callback query';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update);
    }

    /**
     * Command execute method.
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    public function execute()
    {
        /** @var BotManager $botManager */
        $botManager = $this->getTelegram();
        $user = $botManager->getUser();
        $callbackQuery = $this->getCallbackQuery();

        $data = [
            'callback_query_id' => $callbackQuery->getId(),
            'text' => 'Функционал временно недоступен, спасибо!',
            'show_alert' => true,
            'cache_time' => 5,
        ];

        if ($user->getKingdom() instanceof Kingdom) {
            $callback = null;
            $callbackData = CallbackFactory::getData($callbackQuery);
            $callbackName = $callbackData[0] ?? null;

            if ('null' === $callbackName) {
                $data['text'] = '';
            } else {
                /** @var CallbackFactory $callbackFactory */
                $callbackFactory = $botManager->get(CallbackFactory::class);
                if ($callbackFactory->isAvailable($callbackName)) {
                    $callback = $callbackFactory->create($callbackName);
                }

                if (null !== $callback) {
                    return $callback->execute();
                }
            }
        }

        return Request::answerCallbackQuery($data);
    }
}

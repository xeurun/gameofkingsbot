<?php

namespace App\Commands\System;

use App\Commands\BaseCommand;
use App\Entity\Kingdom;
use App\Factory\CallbackFactory;
use App\Manager\BotManager;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class CallbackqueryCommand extends BaseCommand
{
    /**
     * @param BotManager $botManager
     * @param Update|null $update
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'callbackquery';
        $this->description  = 'Reply to callback query';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update, true);
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        /** @var BotManager $botManager */
        $botManager = $this->getTelegram();
        $user = $botManager->getUser();
        $callbackQuery     = $this->getCallbackQuery();

        $data = [
            'callback_query_id' => $callbackQuery->getId(),
            'text'              => 'Функционал временно недоступен, спасибо!',
            'show_alert'        => true,
            'cache_time'        => 5,
        ];

        if ($user->getKingdom() instanceof Kingdom) {
            $callback = null;
            /** @var CallbackFactory $callbackFactory */
            $callbackFactory = $botManager->get(CallbackFactory::class);
            $callbackData = $callbackFactory->getData($callbackQuery);
            $callbackName = $callbackData['n'] ?? null;
            if ($callbackFactory->isAvailable($callbackName)) {
                $callback = $callbackFactory->create(
                    $callbackName,
                    $botManager
                );
            }

            if (null !== $callback) {
                return $callback->execute();
            }
        }

        return Request::answerCallbackQuery($data);
    }
}

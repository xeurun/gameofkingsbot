<?php

namespace App\Callbacks;

use App\Entity\Kingdom;
use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Manager\BotManager;
use App\Manager\ResourceManager;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Symfony\Component\Translation\TranslatorInterface;

class TutorialCallback extends BaseCallback
{
    /**
     * @return ServerResponse
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $data = $this->tutorial();

        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws
     */
    public function tutorial(): array
    {
        $callbackData = CallbackFactory::getData($this->callbackQuery);
        $c = $callbackData[1];
        if ($c) {
            $data = [
                'chat_id' => $this->botManager->getUser()->getId(),
                'text' => <<<TEXT
Склад это бла бла
Это тоже бла бла
TEXT
                ,
                'parse_mode' => 'Markdown'
            ];

            Request::sendMessage($data);
        }

        // TODO: set tutorial 0

        $data = [
            'callback_query_id' => $this->callbackQuery->getId(),
            'show_alert' => false
        ];

        $data['text'] = 'Как прикажете!';

        return $data;
    }
}

<?php

namespace App\Callbacks;

use App\Entity\User;
use App\Interfaces\CallbackInterface;
use App\Interfaces\StateInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class ChangeKingdomNameCallback extends BaseCallback
{
    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $user = $this->botManager->getUser();
        $entityManager = $this->botManager->getEntityManager();

        $response = $this->sendKingdomNameRequest($user);

        if ($response->getOk()) {
            $user->setState(StateInterface::STATE_WAIT_KINGDOM_NAME);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return Request::answerCallbackQuery([
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => '',
            'show_alert' => false,
        ]);
    }

    /**
     * @param User $user ;
     * @return ServerResponse
     * @throws TelegramException
     */
    public function sendKingdomNameRequest(User $user): ServerResponse
    {
        $text = $this->botManager->getTranslator()->trans(
            CallbackInterface::CALLBACK_CHANGE_KINGDOM_NAME,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        $data = [
            'chat_id' => $user->getId(),
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];

        return Request::sendMessage($data);
    }
}

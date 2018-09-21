<?php

namespace App\Callbacks;

use App\Interfaces\StateInterface;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ChangeKingdomNameCallback extends BaseCallback
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $user = $this->botManager->getUser();

        $text = <<<TEXT
*Ваше желание - закон!*

Пришлите новое название вашего королевства
TEXT;

        $data    = [
            'chat_id'      => $user->getId(),
            'text'         => $text,
            'parse_mode'   => 'Markdown'
        ];

        $entityManager = $this->botManager->getEntityManager();
        $user->setState(StateInterface::STATE_WAIT_KINGDOM_NAME);
        $result = Request::sendMessage($data);
        if ($result->getOk()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $data = [
            'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
            'text'              => '',
            'show_alert'        => false,
        ];

        return Request::answerCallbackQuery($data);
    }
}

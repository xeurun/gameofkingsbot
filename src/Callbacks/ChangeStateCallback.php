<?php

namespace App\Callbacks;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Factory\StateFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\StateInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class ChangeStateCallback extends BaseCallback
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $user = $this->botManager->getUser();
        $entityManager = $this->botManager->getEntityManager();

        $callbackData = CallbackFactory::getData($this->callbackQuery);
        $stateName = $callbackData[1];

        $user->setState($stateName);
        $entityManager->persist($user);
        $entityManager->flush();

        $state = null;
        /** @var StateFactory $stateFactory */
        $stateFactory = $this->botManager->get(StateFactory::class);
        $this->botManager->setMessage($this->callbackQuery->getMessage());
        if ($stateFactory->isAvailable($stateName)) {
            $state = $stateFactory->create(
                $stateName,
                $this->botManager
            );
        }

        if (null !== $state) {
            $state->preExecute();
        }

        return Request::answerCallbackQuery([
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => 'Ваше желание - закон!',
            'show_alert' => false,
        ]);
    }
}

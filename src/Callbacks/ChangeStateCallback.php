<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Factory\StateFactory;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ChangeStateCallback extends BaseCallback
{
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
            $state = $stateFactory->create($stateName);
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

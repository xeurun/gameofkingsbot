<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Factory\StateFactory;
use App\Interfaces\StateInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class IncreaseStructureLevelCallback extends BaseCallback
{
    /**
     * {@inheritdoc}
     */
    public function execute(): ServerResponse
    {
        $stateName = StateInterface::STATE_WAIT_INPUT_STRUCTURE_COUNT_FOR_BUY;

        $this->increaseStructureLevel($stateName);

        $response = Request::answerCallbackQuery([
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => 'Ваше слово - закон!',
            'show_alert' => false,
        ]);

        $stateStrategy = null;
        /** @var StateFactory $stateFactory */
        $stateFactory = $this->botManager->get(StateFactory::class);
        if ($stateFactory->isAvailable($stateName)) {
            $stateStrategy = $stateFactory->create($stateName);
        }

        if (null !== $stateStrategy) {
            $stateStrategy->preExecute();
        }

        return $response;
    }

    /**
     * @throws
     */
    public function increaseStructureLevel(string $stateName)
    {
        $callbackData = CallbackFactory::getData($this->callbackQuery);

        $user = $this->botManager->getUser();
        $user->setState(
            $stateName,
            [
                'messageId' => $this->message->getMessageId(),
                'structureCode' => $callbackData[1],
            ]
        );

        $entityManager = $this->botManager->getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }
}

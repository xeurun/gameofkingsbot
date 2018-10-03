<?php

namespace App\Commands\System;

use App\Entity\Kingdom;
use App\Entity\User;
use App\Factory\ScreenFactory;
use App\Factory\StateFactory;
use App\Manager\BotManager;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'genericmessage';
        $this->description = 'Handle generic message';
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

        $result = Request::emptyResponse();

        $message = $botManager->getMessage();
        if ($message) {
            $user = $botManager->getUser();
            $state = $user->getState();
            $stateName = $state[User::STATE_NAME_KEY] ?? null;

            if (null !== $stateName) {
                $stateStrategy = null;
                /** @var StateFactory $stateFactory */
                $stateFactory = $botManager->get(StateFactory::class);
                if ($stateFactory->isAvailable($stateName)) {
                    $stateStrategy = $stateFactory->create($stateName);
                }

                if (null !== $stateStrategy) {
                    $stateStrategy->execute($message);
                }
            }

            if ($user->getKingdom() instanceof Kingdom) {
                $screen = null;
                /** @var ScreenFactory $screenFactory */
                $screenFactory = $botManager->get(ScreenFactory::class);
                $screenName = $message->getText();
                if ($screenFactory->isAvailable($screenName)) {
                    $screen = $screenFactory->create($screenName);
                }

                if (null !== $screen) {
                    $screen->execute();
                }
            }
        }

        return $result;
    }
}

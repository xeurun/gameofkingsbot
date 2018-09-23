<?php

namespace App\Commands\System;

use App\Commands\BaseCommand;
use App\Entity\Kingdom;
use App\Factory\ScreenFactory;
use App\Factory\StateFactory;
use App\Manager\BotManager;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends BaseCommand
{
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'genericmessage';
        $this->description = 'Handle generic message';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update);
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

        $result = Request::emptyResponse();

        $message = $botManager->getMessage();
        if ($message) {
            $user = $botManager->getUser();
            $stateName = $user->getState();
            if (null !== $stateName) {
                $state = null;
                /** @var StateFactory $stateFactory */
                $stateFactory = $botManager->get(StateFactory::class);
                if ($stateFactory->isAvailable($stateName)) {
                    $state = $stateFactory->create(
                        $stateName,
                        $botManager
                    );
                }

                if (null !== $state) {
                    $state->execute();
                }
            }

            if ($user->getKingdom() instanceof Kingdom) {
                $screen = null;
                /** @var ScreenFactory $screenFactory */
                $screenFactory = $botManager->get(ScreenFactory::class);
                $screenName = $message->getText();
                if ($screenFactory->isAvailable($screenName)) {
                    $screen = $screenFactory->create(
                        $screenName,
                        $botManager
                    );
                }

                if (null !== $screen) {
                    $result = $screen->execute();
                }
            }
        }

        return $result;
    }
}

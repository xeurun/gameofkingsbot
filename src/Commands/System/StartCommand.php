<?php

namespace App\Commands\System;

use App\Commands\BaseCommand;
use App\Entity\Kingdom;
use App\Factory\ScreenFactory;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StateInterface;
use App\Manager\BotManager;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Symfony\Component\Translation\TranslatorInterface;

class StartCommand extends BaseCommand
{
    /**
     * @param BotManager $botManager
     * @param Update|null $update
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'start';
        $this->description = 'Start command';
        $this->usage = '/start';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update, true);
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chatId = $message->getChat()->getId();

        /** @var BotManager $botManager */
        $botManager = $this->getTelegram();
        $user = $botManager->getUser();

        $screen = null;
        $result = Request::emptyResponse();

        $text = $botManager->getTranslator()->trans(
            \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING,
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_KINGDOM
        );

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];

        if ($user->getState() === StateInterface::STATE_NEW_PLAYER) {
            $user->setState(StateInterface::STATE_WAIT_KINGDOM_NAME);
            $result = Request::sendMessage($data);
            if ($result->getOk()) {
                $entityManager = $botManager->getEntityManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $result = Request::emptyResponse();
            }
        } else {
            if ($user->getState() === StateInterface::STATE_WAIT_KINGDOM_NAME) {
                $result = Request::sendMessage($data);
            } else {
                if ($user->getKingdom() instanceof Kingdom) {
                    /** @var ScreenFactory $screenFactory */
                    $screenFactory = $botManager->get(ScreenFactory::class);
                    if ($screenFactory->isAvailable(ScreenInterface::SCREEN_MAIN_MENU)) {
                        $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU, $botManager);
                    }

                    if (null !== $screen) {
                        $result = $screen->execute();
                    }
                }
            }
        }

        return $result;
    }
}

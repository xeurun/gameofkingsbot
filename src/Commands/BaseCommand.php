<?php

namespace App\Commands;

use App\Entity\User;
use App\Manager\BotManager;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Update;

abstract class BaseCommand extends SystemCommand
{
    /**
     * @param BotManager $botManager
     * @param Update|null $update
     * @param boolean $createUser
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __construct(BotManager $botManager, Update $update = null, $createUser = false)
    {
        $this->private_only = true;
        $this->need_mysql = false;

        parent::__construct($botManager, $update);

        $from = null;
        if ($this->getCallbackQuery()) {
            $botManager->setCallbackQuery($this->getCallbackQuery());
            $from = $this->getCallbackQuery()->getFrom();
        } else if ($this->getMessage()) {
            $botManager->setMessage($this->getMessage());
            $from = $this->getMessage()->getFrom();
        } else if ($this->getInlineQuery()) {
            $botManager->setInlineQuery($this->getInlineQuery());
            $from = $this->getInlineQuery()->getFrom();
        }

        if ($from) {
            $entityManager = $botManager->getEntityManager();
            $userRepository = $entityManager->getRepository(User::class);

            $user = $userRepository->find($from->getId());
            if (!$user && $createUser) {
                $user = new User($from);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            $botManager->setUser($user);
        }
    }
}

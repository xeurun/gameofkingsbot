<?php

namespace App\Commands\System;

use App\Entity\Kingdom;
use App\Entity\User;
use App\Factory\ScreenFactory;
use App\Factory\StateFactory;
use App\Interfaces\ScreenInterface;
use App\Manager\BotManager;
use App\Repository\UserRepository;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

/**
 * @method BotManager getTelegram()
 */
class StartCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'start';
        $this->description = 'Start command';
        $this->usage = '/start';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update);
    }

    /**
     * Command execute method.
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     */
    public function execute()
    {
        /** @var BotManager $botManager */
        $botManager = $this->getTelegram();
        $user = $botManager->getUser();

        $screen = null;
        $result = Request::emptyResponse();

        $params = explode(' ', $botManager->getMessage()->getText());
        if (isset($params[1]) && $user instanceof User) {
            /** @var UserRepository $userRepository */
            $userRepository = $botManager->getEntityManager()->getRepository(User::class);
            $refer = $userRepository->find($params[1]);
            if ($refer instanceof User) {
                $user->setRefer(
                    $refer
                );
                $botManager->getEntityManager()->persist($user);
                $botManager->getEntityManager()->flush();
            }
        }

        $state = $user->getState();
        $stateName = $state[User::STATE_NAME_KEY] ?? null;

        if (null === $stateName && $user->getKingdom() instanceof Kingdom) {
            /** @var ScreenFactory $screenFactory */
            $screenFactory = $botManager->get(ScreenFactory::class);
            if ($screenFactory->isAvailable(ScreenInterface::SCREEN_MAIN_MENU)) {
                $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU);
            }

            if (null !== $screen) {
                $screen->execute();
            }
        } elseif (null !== $stateName) {
            $stateStrategy = null;
            /** @var StateFactory $stateFactory */
            $stateFactory = $this->getTelegram()->get(StateFactory::class);
            if ($stateFactory->isAvailable($stateName)) {
                $stateStrategy = $stateFactory->create($stateName);
            }

            if (null !== $stateStrategy) {
                $stateStrategy->preExecute();
            }
        }

        return $result;
    }
}

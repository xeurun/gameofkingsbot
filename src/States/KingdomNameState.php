<?php

namespace App\States;

use App\Entity\Kingdom;
use App\Factory\ScreenFactory;
use App\Interfaces\ScreenInterface;
use Doctrine\ORM\ORMException;

class KingdomNameState extends BaseState
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): void
    {
        $kingdomName = trim($this->botManager->getMessage()->getText(true));
        if (!empty($kingdomName)) {
            $entityManager = $this->botManager->getEntityManager();
            $user = $this->botManager->getUser();
            $kingdom = $user->getKingdom();
            if (!$kingdom) {
                $kingdom = new Kingdom($kingdomName, $user);
                $user->setState(null);
                $user->setKingdom($kingdom);
                $entityManager->persist($user);
            } else {
                $kingdom->changeName($kingdomName);
                $user->setState(null);
                $entityManager->persist($user);
            }
            $entityManager->flush();

            $screen = null;
            $screenFactory = $this->botManager->get(ScreenFactory::class);
            if ($screenFactory->isAvailable( ScreenInterface::SCREEN_MAIN_MENU)) {
                $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU, $this->botManager);
            }

            if (null !== $screen) {
                $screen->execute();
            }
        }
    }
}

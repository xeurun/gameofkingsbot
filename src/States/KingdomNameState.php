<?php

namespace App\States;

use App\Entity\Kingdom;
use App\Entity\StructureType;
use App\Factory\ScreenFactory;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Repository\StructureTypeRepository;
use Doctrine\ORM\ORMException;

class KingdomNameState extends BaseState
{
    protected $kingdomManager;

    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->kingdomManager = $kingdomManager;
        parent::__construct($botManager);
    }

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
                $kingdom = $this->kingdomManager->createNewKingdom($kingdomName);
                $user->setKingdom($kingdom);
            } else {
                $kingdom->changeName($kingdomName);
            }
            $entityManager->persist($kingdom);
            $user->setState(null);
            $entityManager->persist($user);
            $entityManager->flush();

            $screen = null;
            /** @var ScreenFactory $screenFactory */
            $screenFactory = $this->botManager->get(ScreenFactory::class);
            if ($screenFactory->isAvailable(ScreenInterface::SCREEN_MAIN_MENU)) {
                $screen = $screenFactory->create(ScreenInterface::SCREEN_MAIN_MENU, $this->botManager);
            }

            if (null !== $screen) {
                $screen->execute();
            }
        }
    }


}

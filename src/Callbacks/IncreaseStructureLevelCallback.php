<?php

namespace App\Callbacks;

use App\Entity\Structure;
use App\Factory\CallbackFactory;
use App\Interfaces\StructureInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Manager\ResourceManager;
use App\Manager\WorkManager;
use App\Repository\StructureTypeRepository;
use App\Screens\BuildingsScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Symfony\Component\Translation\TranslatorInterface;

class IncreaseStructureLevelCallback extends BaseCallback
{
    /** @var CallbackFactory */
    protected $callbackFactory;
    /** @var BuildingsScreen */
    protected $buildingsScreen;
    /** @var KingdomManager */
    protected $kingdomManager;
    /** @var StructureTypeRepository */
    protected $buildTypeRepository;

    /**
     * @param BotManager $botManager
     * @param KingdomManager $kingdomManager
     * @param BuildingsScreen $buildingsScreen
     * @param StructureTypeRepository $buildTypeRepository
     * @param CallbackFactory $callbackFactory
     */
    public function __construct(
        BotManager $botManager,
        KingdomManager $kingdomManager,
        BuildingsScreen $buildingsScreen,
        StructureTypeRepository $buildTypeRepository,
        CallbackFactory $callbackFactory
    ) {
        $this->buildTypeRepository = $buildTypeRepository;
        $this->callbackFactory = $callbackFactory;
        $this->kingdomManager = $kingdomManager;
        $this->buildingsScreen = $buildingsScreen;

        parent::__construct($botManager);
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $data = $this->increaseStructureLevel();
        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws
     */
    public function increaseStructureLevel(): array
    {
        $text = 'Не удалось совершить операцию!';
        $entityManager = $this->botManager->getEntityManager();
        $kingdom = $this->botManager->getKingdom();
        if ($kingdom) {
            $callbackData = $this->callbackFactory->getData($this->callbackQuery);
            $code = $callbackData['c'];
            $build = $kingdom->getStructure($code);
            if (!$build) {
                $buildType = $this->buildTypeRepository->findOneByCode($code);
                if ($buildType) {
                    $build = new Structure($buildType, $kingdom, 0);
                    $kingdom->addStructure($build);
                }
            } else {
                $buildType = $build->getType();
            }

            if ($build) {
                if ($this->kingdomManager->checkAvailableResourceForBuyStructure($kingdom, $buildType)) {
                    $this->kingdomManager->processBuyStructure($kingdom, $build);
                    switch ($code) {
                        case StructureInterface::STRUCTURE_TYPE_CASTLE:
                            $text = 'Вы улучшили свою крепость!';
                            break;
                        case StructureInterface::STRUCTURE_TYPE_TERRITORY:
                            $text = 'Вы расширили свою территорию!';
                            break;
                        default:
                            $text = 'Вы построили новое здание!';
                            break;
                    }

                    $entityManager->persist($kingdom);
                    $entityManager->flush();
                } else {
                    $text = 'Нехватает ресурсов!';
                }
            }
        }

        $message = $this->callbackQuery->getMessage();
        if ($message) {
            $data = $this->buildingsScreen->getMessageData();
            $data['message_id'] = $message->getMessageId();
            Request::editMessageText($data);
        }

        return [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'show_alert' => false,
        ];
    }
}

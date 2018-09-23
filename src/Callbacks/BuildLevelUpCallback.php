<?php

namespace App\Callbacks;

use App\Entity\Build;
use App\Factory\CallbackFactory;
use App\Interfaces\BuildInterface;
use App\Interfaces\CallbackInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\ResourceManager;
use App\Manager\WorkManager;
use App\Repository\BuildTypeRepository;
use App\Screens\BuildingsScreen;
use App\Screens\PeopleScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class BuildLevelUpCallback extends BaseCallback
{
    protected $callbackFactory;
    protected $buildingsScreen;
    protected $peopleManager;
    protected $workManager;
    protected $resourceManager;
    protected $buildTypeRepository;

    public function __construct(
        BotManager $botManager,
        PeopleManager $peopleManager,
        WorkManager $workManager,
        ResourceManager $resourceManager,
        BuildingsScreen $buildingsScreen,
        BuildTypeRepository $buildTypeRepository,
        CallbackFactory $callbackFactory
    ) {
        $this->buildTypeRepository = $buildTypeRepository;
        $this->callbackFactory = $callbackFactory;
        $this->peopleManager = $peopleManager;
        $this->resourceManager = $resourceManager;
        $this->workManager = $workManager;
        $this->buildingsScreen = $buildingsScreen;
        parent::__construct($botManager);
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $callback = $this->botManager->getCallbackQuery();
        $user = $this->botManager->getUser();

        $text = 'Не удалось совершить операцию!';
        $entityManager = $this->botManager->getEntityManager();
        $kingdom = $user->getKingdom();
        if ($kingdom) {
            $callbackData = $this->callbackFactory->getData($callback);
            $code = $callbackData['c'];
            $build = $kingdom->getBuild($code);
            if (!$build) {
                $buildType = $this->buildTypeRepository->findOneByCode($code);
                if ($buildType) {
                    $build = new Build($buildType, $kingdom, 0);
                    $kingdom->setBuild($code, $build);
                }
            } else {
                $buildType = $build->getType();
            }

            if ($build) {
                if ($this->resourceManager->checkAvailableResourceForBuyBuild($kingdom, $buildType)) {
                    $kingdom = $this->resourceManager->processBuyBuild($kingdom, $buildType);
                    $text = $code === BuildInterface::BUILD_TYPE_CASTLE ?
                        'Вы улучшили свою крепость!' : 'Вы построили новое здание!';
                    $build->setLevel($build->getLevel() + 1);
                    $entityManager->persist($kingdom);
                    $entityManager->flush();
                } else {
                    $text = 'Нехватает ресурсов!';
                }
            }
        }

        if ($callback->getMessage()) {
            $data = $this->buildingsScreen->getMessageData();
            $data['message_id'] = $callback->getMessage()->getMessageId();
            Request::editMessageText($data);
        }

        $data = [
            'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
            'text'              => $text,
            'show_alert'        => false,
        ];

        return Request::answerCallbackQuery($data);
    }
}

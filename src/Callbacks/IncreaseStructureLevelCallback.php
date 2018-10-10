<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Factory\StateFactory;
use App\Interfaces\StateInterface;
use App\Manager\BotManager;
use App\Repository\StructureTypeRepository;
use App\States\WaitInputStructureCountForBuyState;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class IncreaseStructureLevelCallback extends BaseCallback
{
    /** @var WaitInputStructureCountForBuyState */
    protected $waitInputStructureCountForBuyState;
    /** @var StructureTypeRepository */
    protected $structureTypeRepository;

    /**
     * IncreaseStructureLevelCallback constructor.
     */
    public function __construct(
        BotManager $botManager,
        WaitInputStructureCountForBuyState $waitInputStructureCountForBuyState,
        StructureTypeRepository $structureTypeRepository
    ) {
        $this->waitInputStructureCountForBuyState = $waitInputStructureCountForBuyState;
        $this->structureTypeRepository = $structureTypeRepository;
        parent::__construct($botManager);
    }

    /**
     * {@inheritdoc}
     * @throws
     */
    public function execute(): ServerResponse
    {
        $stateName = StateInterface::STATE_WAIT_INPUT_STRUCTURE_COUNT_FOR_BUY;

        $callbackData = CallbackFactory::getData($this->callbackQuery);

        $response = Request::answerCallbackQuery([
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => 'Ваше слово - закон!',
            'show_alert' => false,
        ]);

        $structureType = $this->structureTypeRepository->findOneByCode($callbackData[1]);
        $user = $this->botManager->getUser();
        if (
            $this->waitInputStructureCountForBuyState->checkReq(
                $user,
                $structureType,
                1
            )
        ) {
            $this->increaseStructureLevel($stateName);


            $stateStrategy = null;
            /** @var StateFactory $stateFactory */
            $stateFactory = $this->botManager->get(StateFactory::class);
            if ($stateFactory->isAvailable($stateName)) {
                $stateStrategy = $stateFactory->create($stateName);
            }

            if (null !== $stateStrategy) {
                $stateStrategy->preExecute();
            }
        }

        return $response;
    }

    /**
     * @throws
     */
    public function increaseStructureLevel(string $stateName): void
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

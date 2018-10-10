<?php

namespace App\States;

use App\Entity\Structure;
use App\Entity\StructureType;
use App\Entity\User;
use App\Interfaces\StructureInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\StructureManager;
use App\Repository\StructureTypeRepository;
use App\Screens\Edicts\BuildingsScreen;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class WaitInputStructureCountForBuyState extends BaseState
{
    /** @var BuildingsScreen */
    protected $buildingsScreen;
    /** @var KingdomManager */
    protected $kingdomManager;
    /** @var StructureManager */
    protected $structureManager;
    /** @var StructureTypeRepository */
    protected $structureTypeRepository;

    /**
     * WaitInputStructureCountForBuyState constructor.
     */
    public function __construct(
        BotManager $botManager,
        KingdomManager $kingdomManager,
        StructureManager $structureManager,
        BuildingsScreen $buildingsScreen,
        StructureTypeRepository $structureTypeRepository
    ) {
        $this->structureTypeRepository = $structureTypeRepository;
        $this->kingdomManager = $kingdomManager;
        $this->structureManager = $structureManager;
        $this->buildingsScreen = $buildingsScreen;

        parent::__construct($botManager);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function preExecute(): void
    {
        $user = $this->botManager->getUser();

        $gender = $this->botManager->getTranslator()->transChoice(
            TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
            User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
        );

        $state = $user->getState();
        $stateData = $state[User::STATE_DATA_KEY] ?? [];
        $structureCode = $stateData['structureCode'] ?? null;
        $structureType = $this->structureTypeRepository->findOneByCode($structureCode);
        if($structureType && $this->checkReq($user, $structureType, 1)) {
            Request::sendMessage([
                'chat_id' => $this->botManager->getUser()->getId(),
                'text' => $this->botManager->getTranslator()->trans(
                    TranslatorInterface::TRANSLATOR_MESSAGE_INPUT_STRUCTURE_COUNT_FOR_BUY,
                    [
                        '%gender%' => $gender,
                        '%free%' => $this->kingdomManager->getFreeStructureSpace(),
                        '%max%' => $this->structureManager->getMaxAvailableForBuy($structureType)
                    ],
                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                ),
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(Message $message): void
    {
        $user = $this->botManager->getUser();
        $count = trim($message->getText(true));

        $entityManager = $this->botManager->getEntityManager();
        if (\is_numeric($count) && (int)$count > 0) {
            $count = (int)$count;

            $state = $user->getState();
            $stateData = $state[User::STATE_DATA_KEY] ?? [];

            $messageId = $stateData['messageId'] ?? null;
            $structureCode = $stateData['structureCode'] ?? null;

            $kingdom = $this->botManager->getKingdom();

            $structure = $kingdom->getStructure($structureCode);
            if (!$structure) {
                $structureType = $this->structureTypeRepository->findOneByCode($structureCode);
                if ($structureType) {
                    $build = new Structure($structureType, $kingdom, 0);
                    $kingdom->addStructure($build);
                }
            } else {
                $structureType = $structure->getType();
            }

            if ($structure && $this->checkReq($user, $structureType, $count)) {
                $this->structureManager->processBuySomeStructure($structure, $count);
                switch ($structureCode) {
                    case StructureInterface::STRUCTURE_TYPE_CASTLE:
                        $text = $this->botManager->getTranslator()->trans(
                            TranslatorInterface::TRANSLATOR_MESSAGE_CASTLE_LEVEL_UP,
                            [
                                '%gender%' => $this->botManager->getTranslator()->transChoice(
                                    TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                                    User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                                    [],
                                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                                ),
                            ],
                            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                        );

                        break;
                    case StructureInterface::STRUCTURE_TYPE_TERRITORY:
                        $text = $this->botManager->getTranslator()->trans(
                            TranslatorInterface::TRANSLATOR_MESSAGE_TERRITORY_LEVEL_UP,
                            [
                                '%gender%' => $this->botManager->getTranslator()->transChoice(
                                    TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                                    User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                                    [],
                                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                                ),
                            ],
                            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                        );

                        break;
                    default:
                        $text = $this->botManager->getTranslator()->trans(
                            TranslatorInterface::TRANSLATOR_MESSAGE_STRUCTURE_LEVEL_UP,
                            [
                                '%gender%' => $this->botManager->getTranslator()->transChoice(
                                    TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                                    User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                                    [],
                                    TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                                ),
                            ],
                            TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                        );

                        break;
                }
                $entityManager->persist($kingdom);

                if ($messageId) {
                    $data = $this->buildingsScreen->getMessageData();
                    $data['message_id'] = $messageId;
                    Request::editMessageText($data);
                }

                Request::sendMessage([
                    'chat_id' => $user->getId(),
                    'text' => $text,
                    'parse_mode' => 'Markdown',
                ]);

                $user->setState(null);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        } elseif (\is_numeric($count) && 0 === (int)$count) {
            $user->setState(null);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->buildingsScreen->execute();
        } else {
            $this->preExecute();
        }
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function checkReq(User $user, StructureType $structureType, int $count): bool
    {
        $result = false;
        $hasAvailable = $this->structureManager->hasAvailableForSomeStructure($structureType, $count);
        $hasFreeSpace = $this->structureManager->hasFreeSpaceForSomeStructure($structureType, $count);

        if ($hasFreeSpace <= 0 && $hasAvailable <= 0) {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_NO_HAVE_FREE_SPACE_AND_AVAILABLE_RESOURCES,
                [
                    '%gender%' => $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                        User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                    ),
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
            );
        } else if ($hasFreeSpace <= 0) {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_NO_HAVE_FREE_SPACE,
                [
                    '%gender%' => $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                        User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                    ),
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
            );
        } else if ($hasAvailable <= 0) {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_NO_HAVE_AVAILABLE_RESOURCES,
                [
                    '%gender%' => $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                        User::AVAILABLE_GENDER_KING === $user->getGender() ? 1 : 0,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                    ),
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
            );
        } else {
            $result = true;
        }

        if (!$result) {
            Request::sendMessage([
                'chat_id' => $user->getId(),
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        }

        return $result;
    }
}

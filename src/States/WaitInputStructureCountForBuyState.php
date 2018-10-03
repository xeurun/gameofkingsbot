<?php

namespace App\States;

use App\Entity\Structure;
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
    protected $buildTypeRepository;

    /**
     * WaitInputStructureCountForBuyState constructor.
     */
    public function __construct(
        BotManager $botManager,
        KingdomManager $kingdomManager,
        StructureManager $structureManager,
        BuildingsScreen $buildingsScreen,
        StructureTypeRepository $buildTypeRepository
    ) {
        $this->buildTypeRepository = $buildTypeRepository;
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

        Request::sendMessage([
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_INPUT_STRUCTURE_COUNT_FOR_BUY,
                [
                    '%gender%' => $gender,
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_STATE
            ),
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(Message $message): void
    {
        $count = trim($message->getText(true));

        if (\is_numeric($count) && (int)$count > 0) {
            $count = (int)$count;

            $user = $this->botManager->getUser();
            $state = $user->getState();
            $stateData = $state[User::STATE_DATA_KEY] ?? [];

            $messageId = $stateData['messageId'] ?? null;
            $structureCode = $stateData['structureCode'] ?? null;

            $kingdom = $this->botManager->getKingdom();

            $entityManager = $this->botManager->getEntityManager();
            $build = $kingdom->getStructure($structureCode);
            if (!$build) {
                $buildType = $this->buildTypeRepository->findOneByCode($structureCode);
                if ($buildType) {
                    $build = new Structure($buildType, $kingdom, 0);
                    $kingdom->addStructure($build);
                }
            } else {
                $buildType = $build->getType();
            }

            if ($build) {
                $hasAvailable = $this->structureManager->hasAvailableForSomeStructure($buildType, $count);
                $hasFreeSpace = $this->structureManager->hasFreeSpaceForSomeStructure($buildType, $count);
                if ($hasAvailable && $hasFreeSpace) {
                    $this->structureManager->processBuySomeStructure($build, $count);
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
                } elseif (!$hasFreeSpace && !$hasAvailable) {
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
                } elseif (!$hasFreeSpace) {
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
                } else {
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
                }
            }

            $user->setState(null);
            $entityManager->persist($user);
            $entityManager->flush();

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
        } else {
            $this->preExecute();
        }
    }
}

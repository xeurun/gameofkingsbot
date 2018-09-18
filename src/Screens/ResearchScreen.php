<?php

namespace App\Screens;

use App\Factory\CallbackFactory;
use App\Helper\CurrencyHelper;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Repository\ResearchTypeRepository;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class ResearchScreen extends BaseScreen
{
    /** @var ResearchTypeRepository */
    protected $researchTypeRepository;

    /**
     * ResearchScreen constructor.
     */
    public function __construct(BotManager $botManager, ResearchTypeRepository $researchTypeRepository)
    {
        $this->researchTypeRepository = $researchTypeRepository;
        parent::__construct($botManager);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_RESEARCH;

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'parse_mode' => 'Markdown',
        ];

        $library = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_LIBRARY);
        if (!$library) {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE_WITHOUT_LIBRARY,
                [
                    '%title%' => $title,
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );
        } else {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_RESEACRH_SCREEN_MESSAGE,
                [
                    '%title%' => $title,
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
            );

            $researches = [];
            $researchesTypes = $this->researchTypeRepository->findAll();
            foreach ($researchesTypes as $researchType) {
                $cost = [];
                $goldCost = $researchType->getResourceCost(ResourceInterface::RESOURCE_GOLD);
                if ($goldCost > 0) {
                    $cost[] = '💰 ' . CurrencyHelper::costFormat($goldCost);
                }
                $woodCost = $researchType->getResourceCost(ResourceInterface::RESOURCE_WOOD);
                if ($woodCost > 0) {
                    $cost[] = '🌲 ' . CurrencyHelper::costFormat($woodCost);
                }
                $stoneCost = $researchType->getResourceCost(ResourceInterface::RESOURCE_STONE);
                if ($stoneCost > 0) {
                    $cost[] = '⛏ ' . CurrencyHelper::costFormat($stoneCost);
                }
                $ironCost = $researchType->getResourceCost(ResourceInterface::RESOURCE_IRON);
                if ($ironCost > 0) {
                    $cost[] = '🔨' . CurrencyHelper::costFormat($ironCost);
                }

                $researches = [
                    [
                        [
                            'text' => $this->botManager->getTranslator()->trans(
                                $researchType->getCode(),
                                [],
                                TranslatorInterface::TRANSLATOR_DOMAIN_COMMON
                            ),
                            'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_GET_INFO, $researchType->getCode()),
                        ],
                        [
                            'text' => 'Изучить',
                            'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_INCREASE_RESEARCH_LEVEL, 1),
                        ],
                    ],
                ];
            }

            $inlineKeyboard = new InlineKeyboard(
                ...$researches
            );

            $data['reply_markup'] = $inlineKeyboard;
        }

        $data['text'] = $text;

        Request::sendMessage($data);
    }
}

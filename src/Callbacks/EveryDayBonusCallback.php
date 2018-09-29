<?php

namespace App\Callbacks;

use App\Helper\CurrencyHelper;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\ResourceManager;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class EveryDayBonusCallback extends BaseCallback
{
    /** @var ResourceManager */
    protected $resourceManager;

    public function __construct(
        BotManager $botManager,
        ResourceManager $resourceManager
    ) {
        $this->resourceManager = $resourceManager;
        parent::__construct($botManager);
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $data = $this->requestEveryDayBonus();

        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws
     */
    public function requestEveryDayBonus(): array
    {
        $user = $this->botManager->getUser();
        $kingdom = $this->botManager->getKingdom();

        $today = new \DateTime();

        $data = [
            'callback_query_id' => $this->callbackQuery->getId(),
            'show_alert' => false
        ];

        if (!$user->getBonusDate() ||
            $user->getBonusDate()->format('d') !== $today->format('d')
        ) {
            $currentFood = $kingdom->getFood();
            $currentGold = $kingdom->getGold();
            $currentWood = $kingdom->getWood();
            $currentStone = $kingdom->getStone();
            $currentIron = $kingdom->getIron();

            $this->resourceManager->addEveryDayBonus();

            $foodDiff = $kingdom->getFood() - $currentFood;
            $goldDiff = $kingdom->getGold() - $currentGold;
            $woodDiff = $kingdom->getWood() - $currentWood;
            $stoneDiff = $kingdom->getStone() - $currentStone;
            $ironDiff = $kingdom->getIron() - $currentIron;

            $subText = $this->botManager->getTranslator()->trans(
                CallbackInterface::CALLBACK_EVERY_DAY_BONUS,
                [
                    '%gold%' => CurrencyHelper::costFormat($goldDiff),
                    '%food%' => CurrencyHelper::costFormat($foodDiff),
                    '%wood%' => CurrencyHelper::costFormat($woodDiff),
                    '%stone%' => CurrencyHelper::costFormat($stoneDiff),
                    '%iron%' => CurrencyHelper::costFormat($ironDiff)
                ],
                TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );

            Request::sendMessage([
                'chat_id' => $kingdom->getUser()->getId(),
                'text' => $subText,
                'parse_mode' => 'Markdown',
            ]);

            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_EVERY_DAY_BONUS_RECEIVED,
                [],
                TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );

            $entityManager = $this->botManager->getEntityManager();
            $user->setBonusDate($today);
            $entityManager->persist($user);
            $entityManager->persist($kingdom);
            $entityManager->flush();
        } else {
            $text = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_EVERY_DAY_BONUS_ALREADY_RECEIVED,
                [],
                TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );
        }

        $data['text'] = $text;

        return $data;
    }
}

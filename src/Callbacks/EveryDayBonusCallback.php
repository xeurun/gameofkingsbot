<?php

namespace App\Callbacks;

use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class EveryDayBonusCallback extends BaseCallback
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $user = $this->botManager->getUser();
        $today = new \DateTime();

        if (!$user->getBonusDate() || $user->getBonusDate()->format('d') !== $today->format('d')) {
            $bonusGold = 5;
            $bonusFood = 50;
            $bonusWood = 10;
            $bonusStone = 1;
            $bonusMetal = 1;
            $text = <<<TEXT
Спасибо что остаетесь с нами, вот ваша награда!

💰 Золота ({$bonusGold}ед.)
🍞 Еды ({$bonusFood}ед.)
🌲 Дерева ({$bonusWood}ед.)
⛏ Камней ({$bonusStone}ед.)
🔨 Железа ({$bonusMetal}ед.)
TEXT;

            $entityManager = $this->botManager->getEntityManager();
            $user->setBonusDate($today);
            $entityManager->persist($user);
            $kingdom = $user->getKingdom();
            if ($kingdom) {
                $kingdom->setGold($kingdom->getGold() + $bonusGold);
                $kingdom->setFood($kingdom->getFood() + $bonusFood);
                $kingdom->setWood($kingdom->getWood() + $bonusWood);
                $kingdom->setStone($kingdom->getStone() + $bonusStone);
                $kingdom->setMetal($kingdom->getMetal() + $bonusMetal);
            }
            $entityManager->persist($kingdom);
            $entityManager->flush();

            $data = [
                'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
                'text'              => $text,
                'show_alert'        => true,
            ];
        } else {
            $text = <<<TEXT
Сегодня вы уже получали ежедневный бонус!
TEXT;

            $data = [
                'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
                'text'              => $text,
                'show_alert'        => true,
            ];
        }

        return Request::answerCallbackQuery($data);
    }
}

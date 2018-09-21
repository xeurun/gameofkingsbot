<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Screens\PeopleScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class UpDownTaxCallback extends BaseCallback
{
    protected $callbackFactory;
    protected $peopleScreen;
    protected $peopleManager;

    public function __construct(BotManager $botManager, PeopleManager $peopleManager, PeopleScreen $peopleScreen, CallbackFactory $callbackFactory)
    {
        $this->callbackFactory = $callbackFactory;
        $this->peopleManager = $peopleManager;
        $this->peopleScreen = $peopleScreen;
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

        $entityManager = $this->botManager->getEntityManager();
        $kingdom = $user->getKingdom();
        if ($kingdom) {
            $callbackData = $this->callbackFactory->getData($callback);
            if ($callbackData['v'] === '+') {
                $newLevel = 'увеличены';
                $newTax = TaxesInterface::TAXE_HIGH;
                if ($kingdom->getTax() === TaxesInterface::TAXE_LOW) {
                    $newTax = TaxesInterface::TAXE_MEDIUM;
                }
            } else {
                $newLevel = 'уменьшены';
                $newTax = TaxesInterface::TAXE_LOW;
                if ($kingdom->getTax() === TaxesInterface::TAXE_HIGH) {
                    $newTax = TaxesInterface::TAXE_MEDIUM;
                }
            }
            $kingdom->setTax($newTax);
            $entityManager->persist($kingdom);
            $entityManager->flush();
        }

        if ($callback->getMessage()) {
            $data = $this->peopleScreen->getMessageData();
            $data['message_id'] = $callback->getMessage()->getMessageId();
            Request::editMessageText($data);
        }

        $taxLevel = $this->peopleManager->taxLevel($kingdom);
        $text = <<<TEXT
Налоги {$newLevel} до уровня {$taxLevel}
TEXT;
        $data = [
            'callback_query_id' => $this->botManager->getCallbackQuery()->getId(),
            'text'              => $text,
            'show_alert'        => false,
        ];

        return Request::answerCallbackQuery($data);
    }
}

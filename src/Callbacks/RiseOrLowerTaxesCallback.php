<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\TaxesInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Screens\PeopleScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Symfony\Component\Translation\TranslatorInterface;

class RiseOrLowerTaxesCallback extends BaseCallback
{
    /** @var CallbackFactory */
    protected $callbackFactory;
    /** @var PeopleScreen */
    protected $peopleScreen;
    /** @var KingdomManager */
    protected $kingdomManager;
    /** @var PeopleManager */
    protected $peopleManager;

    public function __construct(
        BotManager $botManager,
        TranslatorInterface $translator,
        PeopleManager $peopleManager,
        KingdomManager $kingdomManager,
        PeopleScreen $peopleScreen,
        CallbackFactory $callbackFactory
    ) {
        $this->callbackFactory = $callbackFactory;
        $this->peopleManager = $peopleManager;
        $this->peopleScreen = $peopleScreen;
        $this->kingdomManager = $kingdomManager;
        parent::__construct($botManager, $translator);
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $data = $this->changeTaxLevel();
        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws
     */
    public function changeTaxLevel(): array
    {
        $kingdom = $this->botManager->getKingdom();
        $callbackData = $this->callbackFactory->getData($this->callbackQuery);
        if ($callbackData['v'] === '+') {
            $taxStatus = $this->botManager->getTranslator()->trans(
                \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_RAISE_TAXES,
                [],
                \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );

            $newTax = TaxesInterface::TAXES_LEVEL_HIGH;
            if ($kingdom->getTax() === TaxesInterface::TAXES_LEVEL_LOW) {
                $newTax = TaxesInterface::TAXES_LEVEL_MEDIUM;
            }
        } else {
            $taxStatus = $this->botManager->getTranslator()->trans(
                \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_LOWER_TAXES,
                [],
                \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );
            $newTax = TaxesInterface::TAXES_LEVEL_LOW;
            if ($kingdom->getTax() === TaxesInterface::TAXES_LEVEL_HIGH) {
                $newTax = TaxesInterface::TAXES_LEVEL_MEDIUM;
            }
        }

        $entityManager = $this->botManager->getEntityManager();
        $kingdom->setTax($newTax);
        $entityManager->persist($kingdom);
        $entityManager->flush();

        $message = $this->callbackQuery->getMessage();
        if ($message) {
            $data = $this->peopleScreen->getMessageData();
            $data['message_id'] = $message->getMessageId();
            Request::editMessageText($data);
        }

        $taxLevel = $this->botManager->getTranslator()->transChoice(
            \App\Interfaces\TranslatorInterface::TRANSLATOR_MESSAGE_TAXES_LEVEL,
            $kingdom->getTax(),
            [],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        $text = $this->botManager->getTranslator()->trans(
            CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES,
            [
                '%status%' => $taxStatus,
                '%level%' => mb_strtolower($taxLevel)
            ],
            \App\Interfaces\TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        return [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'show_alert' => false,
        ];
    }
}

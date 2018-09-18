<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\TaxesInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\KingdomManager;
use App\Manager\PeopleManager;
use App\Screens\Edicts\PeopleScreen;
use Doctrine\ORM\ORMException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class RiseOrLowerTaxesCallback extends BaseCallback
{
    /** @var PeopleScreen */
    protected $peopleScreen;
    /** @var KingdomManager */
    protected $kingdomManager;
    /** @var PeopleManager */
    protected $peopleManager;

    public function __construct(
        BotManager $botManager,
        PeopleManager $peopleManager,
        KingdomManager $kingdomManager,
        PeopleScreen $peopleScreen
    ) {
        $this->peopleManager = $peopleManager;
        $this->peopleScreen = $peopleScreen;
        $this->kingdomManager = $kingdomManager;
        parent::__construct($botManager);
    }

    /**
     * @throws TelegramException
     * @throws ORMException
     */
    public function execute(): ServerResponse
    {
        $data = $this->changeTaxLevel();

        return Request::answerCallbackQuery($data);
    }

    /**
     * @throws
     */
    public function changeTaxLevel(): array
    {
        $kingdom = $this->botManager->getKingdom();
        $callbackData = CallbackFactory::getData($this->callbackQuery);
        if ('1' === $callbackData[1]) {
            $taxStatus = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_RAISE_TAXES,
                [],
                TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );

            $newTax = TaxesInterface::TAXES_LEVEL_HIGH;
            if (TaxesInterface::TAXES_LEVEL_LOW === $kingdom->getTax()) {
                $newTax = TaxesInterface::TAXES_LEVEL_MEDIUM;
            }
        } else {
            $taxStatus = $this->botManager->getTranslator()->trans(
                TranslatorInterface::TRANSLATOR_MESSAGE_LOWER_TAXES,
                [],
                TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
            );
            $newTax = TaxesInterface::TAXES_LEVEL_LOW;
            if (TaxesInterface::TAXES_LEVEL_HIGH === $kingdom->getTax()) {
                $newTax = TaxesInterface::TAXES_LEVEL_MEDIUM;
            }
        }

        $entityManager = $this->botManager->getEntityManager();
        $kingdom->setTax($newTax);
        $entityManager->persist($kingdom);
        $entityManager->flush();

        $data = $this->peopleScreen->getMessageData();
        $data['message_id'] = $this->message->getMessageId();
        Request::editMessageText($data);

        $taxLevel = $this->botManager->getTranslator()->transChoice(
            TranslatorInterface::TRANSLATOR_MESSAGE_TAXES_LEVEL,
            $kingdom->getTax(),
            [],
            TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        $text = $this->botManager->getTranslator()->trans(
            CallbackInterface::CALLBACK_RAISE_OR_LOWER_TAXES,
            [
                '%status%' => $taxStatus,
                '%level%' => mb_strtolower($taxLevel),
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_CALLBACK
        );

        return [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'show_alert' => false,
        ];
    }
}

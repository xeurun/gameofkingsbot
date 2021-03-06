<?php

namespace App\Callbacks;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Interfaces\AdviserInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class AdviserCallback extends BaseCallback
{
    public function execute(): ServerResponse
    {
        $data = $this->tutorial();

        return Request::answerCallbackQuery($data);
    }

    /**
     * @throws
     */
    public function tutorial(): array
    {
        $kingdom = $this->botManager->getKingdom();
        $callbackData = CallbackFactory::getData($this->callbackQuery);
        $c = $callbackData[1];
        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'message_id' => $this->message->getMessageId(),
            'parse_mode' => 'Markdown',
        ];
        if ('1' === $c) {
            switch ($kingdom->getAdviserState()) {
                case AdviserInterface::ADVISER_SHOW_INITIAL_TUTORIAL:
                    $name = ScreenInterface::SCREEN_TREASURE;
                    $data['text'] = <<<TEXT
*Советник*: для начала посетите «{$name}»

_(для этого нажмите соответствующую кнопку ниже)_
TEXT;
                    $kingdom->setAdviserState(AdviserInterface::ADVISER_SHOW_WAREHOUSE_TUTORIAL);

                    break;
                case AdviserInterface::ADVISER_SHOW_WAREHOUSE_TUTORIAL:
                    $name = ScreenInterface::SCREEN_EDICTS;
                    $data['text'] = <<<TEXT
*Советник*: теперь давайте я расскажу про «{$name}»

_(нажмите соответствующую кнопку ниже)_
TEXT;
                    $kingdom->setAdviserState(AdviserInterface::ADVISER_SHOW_EDICTS_TUTORIAL);

                    break;
                case AdviserInterface::ADVISER_SHOW_EDICTS_TUTORIAL:
                    $name = ScreenInterface::SCREEN_BUILDINGS;
                    $data['text'] = <<<TEXT
*Советник*: в первую очередь давайте я расскажу вам про тип указов - «{$name}»

_(нажмите соответствующую кнопку ниже)_
TEXT;
                    $kingdom->setAdviserState(AdviserInterface::ADVISER_SHOW_BUILDINGS_TUTORIAL);

                    break;
                case AdviserInterface::ADVISER_SHOW_BUILDINGS_TUTORIAL:
                    $name = ScreenInterface::SCREEN_PEOPLE;
                    $data['text'] = <<<TEXT
*Советник*: а теперь давайте поговорим про тип указов - «{$name}»

_(нажмите соответствующую кнопку ниже)_
TEXT;
                    $kingdom->setAdviserState(AdviserInterface::ADVISER_SHOW_PEOPLE_TUTORIAL);

                    break;
                case AdviserInterface::ADVISER_SHOW_PEOPLE_TUTORIAL:
                    $name = ScreenInterface::SCREEN_BONUSES;
                    $back = ScreenInterface::SCREEN_BACK;
                    $data['text'] = <<<TEXT
*Советник*: давайте перейдем к самому интересному «{$name}»

_(вернитесь на главное меню нажав кнопку {$back}, далее нажмите ниже кнопку {$name})_
TEXT;
                    $kingdom->setAdviserState(AdviserInterface::ADVISER_SHOW_BONUSES_TUTORIAL);

                    break;
                case AdviserInterface::ADVISER_SHOW_BONUSES_TUTORIAL:
                    $gender = $this->botManager->getTranslator()->transChoice(
                        TranslatorInterface::TRANSLATOR_MESSAGE_NEW_KING_GENDER,
                        User::AVAILABLE_GENDER_KING === $this->botManager->getUser()->getGender() ? 1 : 0,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_STATE
                    );

                    $data['text'] = <<<TEXT
*Советник*: ну вот пока и все, если будет о чем вам рассказать еще, я вам обязательно сообщю {$gender}

_(пункты со знаком 🔜 находятся в разработке, мы проинформируем вас когда закончим над ними работать)_
TEXT;
                    $kingdom->setAdviserState(null);

                    break;

            }
            Request::editMessageText($data);
        } else {
            $kingdom->setAdviserState(null);
            Request::deleteMessage($data);
        }

        $this->botManager->getEntityManager()->persist($kingdom);
        $this->botManager->getEntityManager()->flush();

        $data = [
            'callback_query_id' => $this->callbackQuery->getId(),
            'show_alert' => false,
        ];

        $data['text'] = 'Как прикажете!';

        return $data;
    }
}

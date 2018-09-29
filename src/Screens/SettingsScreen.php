<?php

namespace App\Screens;

use App\Entity\User;
use App\Factory\CallbackFactory;
use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use App\Interfaces\StateInterface;
use App\Interfaces\TranslatorInterface;
use App\Repository\UserRepository;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class SettingsScreen extends BaseScreen
{
    /**
     * @inheritdoc
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $user = $this->botManager->getUser();
        $kingdom = $this->botManager->getKingdom();
        /** @var UserRepository $userRepository */
        $userRepository = $this->botManager->getEntityManager()->getRepository(User::class);
        $title = ScreenInterface::SCREEN_SETTINGS;

        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_SETTINGS_SCREEN_MESSAGE,
            [
                '%title%' => $title,
                '%count%' => $userRepository->count([]),
                '%kingdomName%' => $kingdom->getName(),
                '%name%' => $user->getName(),
                '%admin%' => '@alexeystepankov'
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $inlineKeyboard = new InlineKeyboard(
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_CHANGE_USER_NAME_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_CHANGE_STATE, StateInterface::STATE_WAIT_INPUT_NAME)
                ],
            ],
            [
                [
                    'text' => $this->botManager->getTranslator()->trans(
                        TranslatorInterface::TRANSLATOR_MESSAGE_CHANGE_KINGDOM_NAME_BUTTON,
                        [],
                        TranslatorInterface::TRANSLATOR_DOMAIN_INLINE
                    ),
                    'callback_data' => CallbackFactory::pack(CallbackInterface::CALLBACK_CHANGE_STATE, StateInterface::STATE_WAIT_INPUT_KINGDOM_NAME)
                ],
            ]
        );

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);
    }
}

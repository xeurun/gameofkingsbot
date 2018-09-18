<?php

namespace App\Commands\System;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

/**
 * @method BotManager getTelegram()
 */
class InlinequeryCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'inlinequery';
        $this->description = 'Reply to inline query';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update);
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     *
     * @return bool|\Longman\TelegramBot\Entities\ServerResponse
     */
    public function execute()
    {
        $botManager = $this->getTelegram();
        $inlineQuery = $botManager->getInlineQuery();
        $query = $inlineQuery->getQuery();

        $data = [
            'inline_query_id' => $inlineQuery->getId(),
        ];

        $articles = [];

        $user = $botManager->getUser();
        $botName = $botManager->getBotUsername();
        $text = <<<TEXT
*{$user->getName()} ({$user->getUsername()})*
Приглашает вас присоедениться к нему в игре «Мое собственное королевство»
https://t.me/{$botName}?start={$user->getId()}
Начните создавать свое собственное королевство уже сейчас!
TEXT;

        $articles[] = [
            'id' => 'sendRequest',
            'title' => 'Пригласить в игру',
            'description' => <<<TEXT
Отправьте приглашение и получайте бонусы (используется ваше игровое имя и ваш никнейм)

TEXT
            ,
            'message_text' => $text,
            'parse_mode' => 'Markdown',
        ];

        $results = [];
        foreach ($articles as $key => $value) {
            if (false !== stripos($value['title'], $query)) {
                $results[] = $value;
            }
        }

        if (0 === \count($results) || (null === $query || '' === trim($query))) {
            $results = $articles;
        }

        $arrayArticle = [];
        foreach ($results as $article) {
            $arrayArticle[] = new InlineQueryResultArticle($article);
        }

        $arrayJson = '[' . implode(',', $arrayArticle) . ']';
        $data['results'] = $arrayJson;
        $data['cache_time'] = 0;
        $result = Request::answerInlineQuery($data);

        return $result->isOk();
    }
}

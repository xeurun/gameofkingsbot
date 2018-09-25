<?php

namespace App\Commands\System;

use App\Commands\BaseCommand;
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
     * @inheritdoc
     */
    public function __construct(BotManager $botManager, Update $update = null)
    {
        $this->name = 'inlinequery';
        $this->description = 'Reply to inline query';
        $this->version = '1.0.0';

        parent::__construct($botManager, $update);
    }

    /**
     * @return bool|\Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $botManager = $this->getTelegram();
        $inlineQuery = $botManager->getInlineQuery();
        $query = $inlineQuery->getQuery();

        $data = [
            'inline_query_id' => $inlineQuery->getId()
        ];

        $articles = [];

        $user = $botManager->getUser();
        $botName = $botManager->getBotUsername();
        $text = <<<TEXT
*{$user->getFirstName()} {$user->getLastName()} ({$user->getUsername()})*
Приглашает вас присоедениться к нему в игре «Мое собственное королевство»
https://t.me/{$botName}?start={$user->getId()}
Начните создавать свое собственное королевство уже сейчас!
TEXT;

        $articles[] = [
            'id' => 'sendRequest',
            'title' => 'Пригласить в игру',
            'description' => 'Пригласите кого нибудь в игру и если он начнет играть вы получите приятные бонусы',
            'message_text' => $text,
            'parse_mode' => 'Markdown'
        ];

        $results = [];
        foreach ($articles as $key => $value) {
            if (stripos($value['title'], $query) !== false) {
                $results[] = $value;
            }
        }

        if (\count($results) === 0 || (null === $query || trim($query) === '')) {
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

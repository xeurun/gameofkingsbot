<?php

namespace App\Controller;

use App\Interfaces\CallbackInterface;
use App\Manager\BotManager;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HookController extends AbstractController
{
    /** @var BotManager */
    protected $botManager;

    /**
     * @param BotManager $botManager
     * @throws TelegramLogException
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * @Route("/hook")
     */
    public function hook(): Response
    {
        try {
            $this->botManager->handle();
        } catch (TelegramException $ex) {
            // log telegram errors
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            dump($ex);
        }

        return new Response();
    }

    /**
     * @Route("/hook/set")
     */
    public function set(): Response
    {
        try {
            $hookUrl = getenv('HOOK_URL');
            $result = $this->botManager->setWebhook(
                $hookUrl,
                [
                    'certificate',
                    'max_connections' => 100,
                    'allowed_updates' => ["message", "inline_query", "callback_query"]
                ]
            );
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (TelegramException $ex) {
            // log telegram errors
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            dump($ex);
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/callback")
     */
    public function fake(Request $request): Response
    {
        try {
            $m = $request->get('m', '/start');
            $callbackName = $request->get('c', CallbackInterface::CALLBACK_EVERY_DAY_BONUS);

            $this->botManager->setCustomInput('{
                "update_id":119409613, 
                "callback_query":{
                    "id":"820339762695174636",
                    "from":{
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "message":{
                        "message_id":676,
                        "from":{
                            "id":642701144,
                            "is_bot":true,
                            "first_name":"Game of Kings",
                            "username":"gameofkingsbot"
                        },
                        "chat":{
                            "id":191000234,
                            "first_name":"Alexey",
                            "last_name":"Stepankov",
                            "username":"alexeystepankov",
                            "type":"private"
                        },
                        "date":1537485979,
                        "text":"' . $m . '",
                        "entities":[
                            {"offset":0,"length":9,"type":"bold"}
                        ]
                    },
                    "chat_instance":"-1983157652211501556",
                    "data":"{\"n\":\"' . $callbackName . '\",\"c\":\"barn\",\"v\":\"+\"}"
                }
            }');

            $this->botManager->handle();
        } catch (TelegramException $ex) {
            // log telegram errors
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            dump($ex);
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/command")
     */
    public function fakeCommand(Request $request): Response
    {
        try {
            $c = $request->get('m', '/start');

            $this->botManager->setCustomInput('{
                "update_id":119409820, 
                "message":{
                    "message_id":972,
                    "from": {
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "chat": {
                        "id":191000234,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "type":"private"
                    },
                    "date":1537514492,
                    "text":"' . $c . '",
                    "entities":[
                        {
                            "offset":0,
                            "length":6,
                            "type":"' . $c . '"
                        }
                    ]
                }
            }');
            $this->botManager->handle();
        } catch (TelegramException $ex) {
            // log telegram errors
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            dump($ex);
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/message")
     */
    public function fakeMessage(Request $request): Response
    {
        try {
            $m = $request->get('m', '/start');

            $this->botManager->setCustomInput('{
                "update_id":119409284, 
                "message": {
                    "message_id":84,
                    "from":{
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "chat":{
                        "id":191000234,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "type":"private"
                    },
                    "date":1537300010,
                    "text":"' . $m . '"
                }
            }');
            $this->botManager->handle();
        } catch (TelegramException $ex) {
            // log telegram errors
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            dump($ex);
        }

        return new Response();
    }

    /**
     * @Route("/hook/fake/inline")
     */
    public function inlineMessage(Request $request): Response
    {
        try {
            $m = $request->get('m', '/start');

            $this->botManager->setCustomInput('{
                "update_id":284751997, 
                "inline_query":{
                    "id":"820339762720782670",
                    "from":{
                        "id":191000234,
                        "is_bot":false,
                        "first_name":"Alexey",
                        "last_name":"Stepankov",
                        "username":"alexeystepankov",
                        "language_code":"en-US"
                    },
                    "query":"' . $m . '","offset":""
                }
            }');
            $this->botManager->handle();
        } catch (TelegramException $ex) {
            // log telegram errors
            dump($ex);
        } catch (\Throwable $ex) {
            // log telegram errors
            dump($ex);
        }

        return new Response();
    }
}

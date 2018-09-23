<?php

namespace App\Controller;

use App\Commands\System\GenericmessageCommand;
use App\Commands\System\StartCommand;
use App\Manager\BotManager;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\TelegramLog;
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
    public function hook()
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
    public function set()
    {
        try {
            $hookUrl = getenv('HOOK_URL');
            $result = $this->botManager->setWebhook($hookUrl);
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
     * @Route("/hook/fake")
     */
    public function fake(Request $request)
    {
        try {
            $m = $request->get('m', '/start');
            $c = (int)$request->get('c', 0);

            if ($c === 1) {
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
            } else if ($c === 0) {
                $this->botManager->setCustomInput('{
                "update_id":119409820, 
                "message":{
                "message_id":972,
                "from":{
                "id":508453111,
                "is_bot":false,
                "first_name":"\ud83d\udd25\ud83c\udf38",
                "last_name":"\u0426\u0432\u0435\u0442",
                "username":"banzisnsbd","language_code":"ru"},
                "chat":{"id":508453111,"first_name":"\ud83d\udd25\ud83c\udf38",
                "last_name":"\u0426\u0432\u0435\u0442","username":"banzisnsbd",
                "type":"private"},"date":1537514492,"text":"/start",
                "entities":[{"offset":0,"length":6,
                "type":"bot_command"}]}}');
            } else if ($c === 2) {
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
                        "data":"{\"n\":\"build_level_up\",\"c\":\"barn\"}"
                    }
                }');
            }
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

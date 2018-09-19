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
            $text = $request->get('message', '/survey');
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
            "text":"' . $text . '",
            "entities":[
                {"offset":0,"length":6,"type":"bot_command"}
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
}

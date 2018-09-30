<?php

namespace App\Callbacks;

use App\Factory\CallbackFactory;
use App\Interfaces\ResourceInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TaxesInterface;
use App\Interfaces\WorkInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class GetInfoCallback extends BaseCallback
{
    /**
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $data = $this->showInfo();

        return Request::answerCallbackQuery($data);
    }

    /**
     * @return array
     * @throws
     */
    public function showInfo(): array
    {
        $callbackData = CallbackFactory::getData($this->callbackQuery);
        $t = $callbackData[1];

        switch ($t) {
            case TaxesInterface::TAXES:
                $text = <<<TEXT
Налоги определяют размер золота и производительность рабочих, а так же количество употребляемой еды
TEXT;
                break;
            case ResourceInterface::RESOURCE_FOOD:
                $text = <<<TEXT
Еда
TEXT;
                break;
            case ResourceInterface::RESOURCE_WOOD:
                $text = <<<TEXT
Древесина
TEXT;
                break;
            case ResourceInterface::RESOURCE_STONE:
                $text = <<<TEXT
Камень 
TEXT;
                break;
            case ResourceInterface::RESOURCE_IRON:
                $text = <<<TEXT
Железо необходимо
TEXT;
                break;
            case WorkInterface::WORK_TYPE_FOOD:
                $text = <<<TEXT
Еда необходима для постройки некоторых сооружений
TEXT;
                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $text = <<<TEXT
Дерево необходимо для постройки некоторых сооружений
TEXT;
                break;
            case WorkInterface::WORK_TYPE_STONE:
                $text = <<<TEXT
Камни необходимы для постройки некоторых сооружений
TEXT;
                break;
            case WorkInterface::WORK_TYPE_IRON:
                $text = <<<TEXT
Железо необходимо для постройки некоторых сооружений
TEXT;
                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $text = <<<TEXT
Армия защищает ваше королевство и может атаковать чужие
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_MARKET:
                $text = <<<TEXT
Рынок открывает доступ к обмену и торговли
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_LIBRARY:
                $text = <<<TEXT
Библиотека открывает доступ к исследованиям и открывает новые исследования
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_CASTLE:
                $text = <<<TEXT
Замок увеличивает общий уровень вашего королевства
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_TERRITORY:
                $text = <<<TEXT
Территория увеличивает доступное количество мест для постройки
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE:
                $text = <<<TEXT
Жилые дома увеличивают количество людей в вашем королевства
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_GARRISON:
                $text = <<<TEXT
Гарнизон увеличивает размер вашей армии
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_SAWMILL:
                $text = <<<TEXT
Лесопила увеличивает вместимость хранилища дерева и количество рабочих на добыче дерева
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_STONEMASON:
                $text = <<<TEXT
Каменоломня увеличивает вместимость хранилища камней и количество рабочих на добыче камня
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_SMELTERY:
                $text = <<<TEXT
Плавильня увеличивает вместимость хранилища железа и количество рабочих на железа
TEXT;
                break;
            case StructureInterface::STRUCTURE_TYPE_BARN:
                $text = <<<TEXT
Амбар увеличивает вместимость хранилища еды и количество рабочих на добыче еды
TEXT;
                break;
            default:
                $text = 'Не найдена запрашиваемая информация!';
                break;
        }

        return  [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'parse_mode' => 'Markdown',
            'show_alert' => true
        ];
    }
}

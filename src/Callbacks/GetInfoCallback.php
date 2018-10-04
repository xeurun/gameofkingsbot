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
    public function execute(): ServerResponse
    {
        $data = $this->showInfo();

        return Request::answerCallbackQuery($data);
    }

    /**
     * @throws
     */
    public function showInfo(): array
    {
        $callbackData = CallbackFactory::getData($this->callbackQuery);
        $t = $callbackData[1];

        switch ($t) {
            case TaxesInterface::TAXES:
                $text = <<<TEXT
Налоги определяют размер золота и производительность подданных, а так же количество употребляемой еды
TEXT;

                break;
            case ResourceInterface::RESOURCE_FOOD:
                $text = <<<TEXT
Еда ресурс необходимый подданным для выживания
TEXT;

                break;
            case ResourceInterface::RESOURCE_WOOD:
                $text = <<<TEXT
Древесина ресурс для постройки
TEXT;

                break;
            case ResourceInterface::RESOURCE_STONE:
                $text = <<<TEXT
Камень ресурс для постройки
TEXT;

                break;
            case ResourceInterface::RESOURCE_IRON:
                $text = <<<TEXT
Железо ресурс для постройки
TEXT;

                break;
            case WorkInterface::WORK_TYPE_FOOD:
                $text = <<<TEXT
Добыча еды позволяет получать необходимый подданным ресурс - еду
TEXT;

                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $text = <<<TEXT
Добыча дерева для получения ресурса - дерево
TEXT;

                break;
            case WorkInterface::WORK_TYPE_STONE:
                $text = <<<TEXT
Добыча камня для получения ресурса - камень
TEXT;

                break;
            case WorkInterface::WORK_TYPE_IRON:
                $text = <<<TEXT
Добыча железа для получения ресурса - железо
TEXT;

                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $text = <<<TEXT
Армия защищает ваше королевство, а также может атаковать чужое
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_MARKET:
                $text = <<<TEXT
Рынок открывает доступ к обмену и торговле
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_LIBRARY:
                $text = <<<TEXT
Библиотека открывает доступ к исследованиям, ее улучшение открывает новые исследования
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_CASTLE:
                $text = <<<TEXT
Улучшение замока увеличивает общий уровень вашего королевства
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_TERRITORY:
                $text = <<<TEXT
Увеличения территории увеличивает доступное количество мест для постройки
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE:
                $text = <<<TEXT
Жилые дома увеличивают ваше количество подданных
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_GARRISON:
                $text = <<<TEXT
Гарнизон увеличивает максимальный размер вашей армии
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_SAWMILL:
                $text = <<<TEXT
Лесопилка увеличивает максимальное количество хранимого дерева на складе, а также количество подданных на его добыче
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_STONEMASON:
                $text = <<<TEXT
Каменоломня увеличивает максимальное количество хранимых камней на складе, а также количество подданных на их добыче
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_SMELTERY:
                $text = <<<TEXT
Плавильня увеличивает максимальное количество хранимого железа на складе, а также количество подданных на его добыче
TEXT;

                break;
            case StructureInterface::STRUCTURE_TYPE_BARN:
                $text = <<<TEXT
Амбар увеличивает максимальное количество хранимой еды на складе, а также количество подданных на ее добыче
TEXT;

                break;
            default:
                $text = 'Извините, не найдена информация по запрашиваемому объекту!';

                break;
        }

        return  [
            'callback_query_id' => $this->callbackQuery->getId(),
            'text' => $text,
            'parse_mode' => 'Markdown',
            'show_alert' => true,
        ];
    }
}

<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Entity\Structure;
use App\Entity\StructureType;
use App\Interfaces\ResourceInterface;
use App\Interfaces\StructureInterface;
use App\Interfaces\TaxesInterface;
use App\Interfaces\WorkInterface;
use App\Repository\StructureTypeRepository;
use Symfony\Bundle\MakerBundle\Str;

class KingdomManager
{
    /** @var BotManager  */
    protected $botManager;

    /**
     * @param BotManager $botManager
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * @param string $kingdomName
     * @return Kingdom
     */
    public function createNewKingdom(string $kingdomName): Kingdom
    {
        /** @var StructureTypeRepository $buildTypeRepository */
        $buildTypeRepository = $this->botManager
            ->getEntityManager()
            ->getRepository(StructureType::class);

        $user = $this->botManager->getUser();
        $kingdom = new Kingdom($kingdomName, $user);

        $castleStructureType = $buildTypeRepository->findOneByCode(StructureInterface::STRUCTURE_TYPE_CASTLE);
        $castleStructure = new Structure($castleStructureType, $kingdom, StructureInterface::INITIAL_STRUCTURE_LEVEL);
        $kingdom->addStructure($castleStructure);

        $territoryStructureType = $buildTypeRepository->findOneByCode(StructureInterface::STRUCTURE_TYPE_TERRITORY);
        $territoryStructure = new Structure($territoryStructureType, $kingdom, StructureInterface::INITIAL_STRUCTURE_LEVEL);
        $kingdom->addStructure($territoryStructure);

        return $kingdom;
    }

    /**
     * @return int
     */
    public function getStructureCount(): int
    {
        $kingdom = $this->botManager->getKingdom();
        $count = 0;
        foreach ($kingdom->getStructures() as $structure) {
            $count += $structure->getLevel();
        }

        return $count;
    }

    /**
     * @return float|int
     */
    public function getTerritorySize()
    {
        $kingdom = $this->botManager->getKingdom();
        return $this->level($kingdom, StructureInterface::STRUCTURE_TYPE_TERRITORY) * StructureInterface::STRUCTURE_TYPE_TERRITORY_ADD_SIZE;
    }

    /**
     * @return string
     */
    public function getTax(): string
    {
        $kingdom = $this->botManager->getKingdom();

        switch ($kingdom->getTax()) {
            case TaxesInterface::TAXES_LEVEL_LOW:
                $taxes = TaxesInterface::TAXES_LOW;
                break;
            case TaxesInterface::TAXES_LEVEL_MEDIUM:
                $taxes = TaxesInterface::TAXES_MEDIUM;
                break;
            case TaxesInterface::TAXES_LEVEL_HIGH:
                $taxes = TaxesInterface::TAXES_HIGH;
                break;
            default:
                $taxes = TaxesInterface::TAXES_CUSTOM;
                break;
        }

        return $taxes;
    }

    /**
     * @param Kingdom $kingdom
     * @param string $newTaxLevel
     * @param int $newTaxLevelValue
     */
    public function setTax(Kingdom $kingdom, string $newTaxLevel, ?int $newTaxLevelValue = null): void
    {
        switch ($newTaxLevel) {
            case TaxesInterface::TAXES_LOW:
                $kingdom->setTax(1);
                break;
            case TaxesInterface::TAXES_MEDIUM:
                $kingdom->setTax(2);
                break;
            case TaxesInterface::TAXES_HIGH:
                $kingdom->setTax(3);
                break;
            case TaxesInterface::TAXES_CUSTOM && null !== $newTaxLevelValue:
                $kingdom->setTax($newTaxLevelValue);
                break;
            default:
                throw new \InvalidArgumentException('Invalid custom tax value');
        }
    }

    public function getPeople()
    {
        $kingdom = $this->botManager->getKingdom();
        $level = $this->level($kingdom, StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE);

        return ResourceInterface::INITIAL_PEOPLE + ($level * StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE_ADD_PEOPLE);
    }

    /**
     * @param Kingdom $kingdom
     * @param string $structureType
     * @return float
     */
    public function level(Kingdom $kingdom, string $structureType): float
    {
        $castle = $kingdom->getStructure($structureType);
        if ($castle) {
            $level = $castle->getLevel();
        } else {
            $level = 0;
        }

        return $level;
    }

    /**
     * @param string $resourceType
     * @return float
     */
    public function getMax(string $resourceType): float
    {
        $kingdom = $this->botManager->getKingdom();

        $level = 1;

        switch($resourceType) {
            case ResourceInterface::RESOURCE_PEOPLE:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_PEOPLE_MAX;
                break;
            case ResourceInterface::RESOURCE_GOLD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SMELTERY);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_GOLD_MAX;
                break;
            case ResourceInterface::RESOURCE_FOOD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_BARN);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_FOOD_MAX;
                break;
            case ResourceInterface::RESOURCE_WOOD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SAWMILL);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_WOOD_MAX;
                break;
            case ResourceInterface::RESOURCE_STONE:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_STONEMASON);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_STONE_MAX;
                break;
            case ResourceInterface::RESOURCE_IRON:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SMELTERY);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_IRON_MAX;
                break;
            default:
                throw new \InvalidArgumentException('Incorrect resource type: ' . $resourceType);
        }

        return (float)($initialCount * $level);
    }

    /**
     * @param string $workType
     * @return float
     */
    public function getMaxOn(string $workType): int
    {
        $kingdom = $this->botManager->getKingdom();

        $level = 1;

        switch($workType) {
            case WorkInterface::WORK_TYPE_ARMY:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_GARRISON);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = WorkInterface::INITIAL_ON_ARMY;
                break;
            case WorkInterface::WORK_TYPE_FOOD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_BARN);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = WorkInterface::INITIAL_ON_FOOD;
                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SAWMILL);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = WorkInterface::INITIAL_ON_WOOD;
                break;
            case WorkInterface::WORK_TYPE_STONE:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_STONEMASON);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = WorkInterface::INITIAL_ON_STONE;
                break;
            case WorkInterface::WORK_TYPE_IRON:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SMELTERY);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = WorkInterface::INITIAL_ON_IRON;
                break;
            default:
                throw new \InvalidArgumentException('Incorrect work type: ' . $workType);
        }

        return round($initialCount * $level);
    }
}

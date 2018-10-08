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

class KingdomManager
{
    /** @var BotManager */
    protected $botManager;

    /**
     * KingdomManager constructor.
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * Create new kingdom.
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
     * Get structure count.
     */
    public function getStructureCount(): int
    {
        $kingdom = $this->botManager->getKingdom();
        $count = 0;
        foreach ($kingdom->getStructures() as $structure) {
            if (StructureInterface::STRUCTURE_TYPE_TERRITORY !== $structure->getType()->getCode()) {
                $count += $structure->getLevel();
            }
        }

        return $count;
    }

    /**
     * Get territory size.
     */
    public function getTerritorySize()
    {
        $kingdom = $this->botManager->getKingdom();

        return $this->getCurrentStructureLevel(
            $kingdom,
            StructureInterface::STRUCTURE_TYPE_TERRITORY
            )
            * StructureInterface::STRUCTURE_TYPE_TERRITORY_ADD_SIZE;
    }

    /**
     * Get tax.
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
     * Set tax.
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

    /**
     * Get people count.
     */
    public function getPeople(): int
    {
        $kingdom = $this->botManager->getKingdom();
        $level = $this->getCurrentStructureLevel(
            $kingdom,
            StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE
        );

        return $kingdom->getResource(ResourceInterface::RESOURCE_PEOPLE) +
            ($level * StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE_ADD_PEOPLE);
    }

    /**
     * Get structure level.
     */
    public function getCurrentStructureLevel(Kingdom $kingdom, string $structureType): float
    {
        $structure = $kingdom->getStructure($structureType);
        if ($structure) {
            $level = $structure->getLevel();
        } else {
            $level = 0;
        }

        return $level;
    }

    /**
     * Get max resource.
     */
    public function getMax(string $resourceType): float
    {
        $kingdom = $this->botManager->getKingdom();

        $level = 1;

        switch ($resourceType) {
            case ResourceInterface::RESOURCE_PEOPLE:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_PEOPLE_MAX * $level;

                break;
            case ResourceInterface::RESOURCE_GOLD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_PUNISHMENT);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_GOLD_MAX * $level;

                break;
            case ResourceInterface::RESOURCE_FOOD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_BARN);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_FOOD_MAX * $level;

                break;
            case ResourceInterface::RESOURCE_WOOD:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SAWMILL);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_WOOD_MAX * $level;

                break;
            case ResourceInterface::RESOURCE_STONE:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_STONEMASON);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_STONE_MAX * $level;

                break;
            case ResourceInterface::RESOURCE_IRON:
                $structure = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_SMELTERY);
                if ($structure) {
                    $level = $structure->getLevel();
                }
                $initialCount = ResourceInterface::INITIAL_IRON_MAX * $level;

                break;
            default:
                throw new \InvalidArgumentException('Incorrect resource type: ' . $resourceType);
        }

        return (float)($initialCount * $level);
    }

    /**
     * Get max workers.
     */
    public function getMaxOn(string $workType): int
    {
        $kingdom = $this->botManager->getKingdom();

        $level = 1;

        switch ($workType) {
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

<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Entity\Structure;
use App\Entity\StructureType;
use App\Interfaces\StructureInterface;
use App\Interfaces\TaxesInterface;
use App\Repository\StructureTypeRepository;
use Symfony\Bundle\MakerBundle\Str;

class KingdomManager
{
    protected $botManager;

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

    public function getTerritorySize(Kingdom $kingdom)
    {
        return $this->territory($kingdom) * StructureInterface::STRUCTURE_TYPE_TERRITORY_ADD_SIZE;
    }

    public function checkAvailableResourceForBuyStructure(Kingdom $kingdom, StructureType $buildType)
    {
        return $kingdom->getGold() >= $buildType->getGoldCost() &&
            $kingdom->getWood() >= $buildType->getWoodCost() &&
            $kingdom->getStone() >= $buildType->getStoneCost() &&
            $kingdom->getIron() >= $buildType->getIronCost();
    }

    public function processBuyStructure(Kingdom $kingdom, Structure $build)
    {
        $structureType = $build->getType();
        $kingdom->setGold($kingdom->getGold() - $structureType->getGoldCost());
        $kingdom->setWood($kingdom->getWood() - $structureType->getWoodCost());
        $kingdom->setStone($kingdom->getStone() - $structureType->getStoneCost());
        $kingdom->setIron($kingdom->getIron() - $structureType->getIronCost());

        $build->setLevel($build->getLevel() + 1);

        switch ($structureType->getCode()) {
            case StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE:
                $kingdom->setPeople($kingdom->getPeople() + StructureInterface::STRUCTURE_TYPE_LIFE_HOUSE_ADD_PEOPLE);
                break;
        }
    }

    /**
     * @param Kingdom $kingdom
     * @return string
     */
    public function getTax(Kingdom $kingdom): string
    {
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

    /**
     * @param Kingdom $kingdom
     * @return float
     */
    public function territory(Kingdom $kingdom): float
    {
        $territory = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_TERRITORY);
        if ($territory) {
            $level = $territory->getLevel();
        } else {
            $level = 0;
        }

        return $level;
    }

    /**
     * @param Kingdom $kingdom
     * @return float
     */
    public function level(Kingdom $kingdom): float
    {
        $castle = $kingdom->getStructure(StructureInterface::STRUCTURE_TYPE_CASTLE);
        if ($castle) {
            $level = $castle->getLevel();
        } else {
            $level = 0;
        }

        return $level;
    }
}

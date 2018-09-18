<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180923001401 extends AbstractMigration
{
    /** @var array */
    protected static $structureTypes = [
        [
            "'structure_type_castle'",
            0,
            5000,
            10000,
            5000,
            3500,
        ],
        [
            "'structure_type_wall'",
            0,
            1000,
            2500,
            1000,
            500,
        ],
        [
            "'structure_type_territory'",
            0,
            1000,
            2500,
            1000,
            500,
        ],
        [
            "'structure_type_punishment'",
            0,
            250,
            1000,
            450,
            250,
        ],
        [
            "'structure_type_lifehouse'",
            0,
            250,
            1000,
            450,
            250,
        ],
        [
            "'structure_type_barn'",
            0,
            250,
            1000,
            450,
            250,
        ],
        [
            "'structure_type_sawmill'",
            0,
            250,
            1000,
            450,
            250,
        ],
        [
            "'structure_type_stonemason'",
            0,
            250,
            1000,
            450,
            250,
        ],
        [
            "'structure_type_smeltery'",
            0,
            250,
            1000,
            450,
            250,
        ],
        [
            "'structure_type_garrison'",
            0,
            5000,
            10000,
            5000,
            3500,
        ],
        [
            "'structure_type_library'",
            1,
            50000,
            100000,
            50000,
            35000,
        ],
        [
            "'structure_type_market'",
            1,
            15000,
            30000,
            15000,
            10000,
        ],
    ];

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $values = [];
        $order = 1;
        foreach (self::$structureTypes as $buildType) {
            $buildType[] = $order++;
            $values[] = '(' . implode(',', $buildType) . ')';
        }

        $this->addSql(
            'INSERT IGNORE INTO structure_type (`code`, `max_level`, `gold_cost`, `wood_cost`, `stone_cost`, `iron_cost`, `order`) VALUES '
            . implode(',', $values)
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE structure_type');
    }
}

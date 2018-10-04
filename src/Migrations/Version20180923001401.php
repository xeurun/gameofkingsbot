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
    protected static $buildTypes = [
        [
            "'castle'",
            0,
            10000,
            15000,
            100,
            100,
        ],
        [
            "'wall'",
            0,
            5000,
            10000,
            50,
            50,
        ],
        [
            "'territory'",
            0,
            5000,
            10000,
            50,
            50,
        ],
        [
            "'lifehouse'",
            0,
            3000,
            10000,
            50,
            50,
        ],
        [
            "'barn'",
            0,
            3000,
            10000,
            50,
            50,
        ],
        [
            "'sawmill'",
            0,
            3000,
            10000,
            50,
            50,
        ],
        [
            "'stonemason'",
            0,
            3000,
            10000,
            50,
            50,
        ],
        [
            "'smeltery'",
            0,
            3000,
            10000,
            50,
            50,
        ],
        [
            "'garrison'",
            0,
            3000,
            10000,
            50,
            50,
        ],
        [
            "'library'",
            1,
            100000,
            100000,
            100000,
            100000,
        ],
        [
            "'market'",
            1,
            100000,
            100000,
            100000,
            100000,
        ],
    ];

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $values = [];
        $order = 1;
        foreach (self::$buildTypes as $buildType) {
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

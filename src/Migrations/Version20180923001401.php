<?php declare(strict_types=1);

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
            1,
            0,
            10000,
            15000,
            100,
            100
        ],
        [
            "'territory'",
            2,
            0,
            5000,
            10000,
            50,
            50
        ],
        [
            "'lifehouse'",
            3,
            0,
            3000,
            10000,
            50,
            50
        ],
        [
            "'barn'",
            4,
            0,
            3000,
            10000,
            50,
            50
        ],
        [
            "'sawmill'",
            5,
            0,
            3000,
            10000,
            50,
            50
        ],
        [
            "'stonemason'",
            6,
            0,
            3000,
            10000,
            50,
            50
        ],
        [
            "'smeltery'",
            7,
            0,
            3000,
            10000,
            50,
            50
        ],
        [
            "'garrison'",
            8,
            0,
            3000,
            10000,
            50,
            50
        ],
        [
            "'library'",
            9,
            1,
            100000,
            100000,
            100000,
            100000
        ],
        [
            "'market'",
            10,
            1,
            100000,
            100000,
            100000,
            100000,
        ]
    ];

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $values = [];
        foreach (self::$buildTypes as $buildType) {
            $values[] = '(' . implode(',', $buildType) . ')';
        }

        $this->addSql(
            'INSERT IGNORE INTO structure_type (code, order, max_level, gold_cost, wood_cost, stone_cost, iron_cost) VALUES '
            . implode(',', $values)
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE structure_type');
    }
}

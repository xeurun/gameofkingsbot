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
            100000,
            100000,
            100000,
            100000,
            1
        ],
        [
            "'territory'",
            5000,
            100000,
            5000,
            3500,
            2
        ],
        [
            "'lifehouse'",
            1000,
            20000,
            500,
            50,
            3
        ],
        [
            "'barn'",
            10000,
            10000,
            10000,
            10000,
            4
        ],
        [
            "'sawmill'",
            10000,
            10000,
            10000,
            10000,
            5
        ],
        [
            "'stonemason'",
            10000,
            10000,
            10000,
            10000,
            6
        ],
        [
            "'smeltery'",
            10000,
            10000,
            10000,
            10000,
            7
        ],
        [
            "'library'",
            10000,
            10000,
            10000,
            10000,
            8
        ],
        [
            "'market'",
            10000,
            10000,
            10000,
            10000,
            9
        ],
        [
            "'garrison'",
            10000,
            10000,
            10000,
            10000,
            10
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
            'INSERT IGNORE INTO structure_type (code, gold_cost, wood_cost, stone_cost, iron_cost, order) VALUES '
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

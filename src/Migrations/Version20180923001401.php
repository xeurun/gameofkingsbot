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
            "'Замок'",
            100000,
            100000,
            100000,
            100000,
            10,
        ],
        [
            "'territory'",
            "'Территория'",
            5000,
            100000,
            5000,
            3500,
            10,
        ],
        [
            "'lifehouse'",
            "'Жилое здание'",
            1000,
            20000,
            500,
            50,
            10,
        ],
        [
            "'barn'",
            "'Амбар'",
            10000,
            10000,
            10000,
            10000,
            10,
        ],
        [
            "'sawmill'",
            "'Лесопилка'",
            10000,
            10000,
            10000,
            10000,
            10,
        ],
        [
            "'stonemason'",
            "'Каменоломня'",
            10000,
            10000,
            10000,
            10000,
            10,
        ],
        [
            "'smeltery'",
            "'Плавильня'",
            10000,
            10000,
            10000,
            10000,
            10,
        ],
        [
            "'library'",
            "'Библиотека'",
            10000,
            10000,
            10000,
            10000,
            10,
        ],
        [
            "'market'",
            "'Рынок'",
            10000,
            10000,
            10000,
            10000,
            10,
        ],
        [
            "'garrison'",
            "'Гарнизон'",
            10000,
            10000,
            10000,
            10000,
            10,
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
            'INSERT IGNORE INTO structure_type (code, name, gold_cost, wood_cost, stone_cost, iron_cost, time_cost) VALUES '
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

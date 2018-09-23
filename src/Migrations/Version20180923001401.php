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
            "'Замок'",
            "'castle'",
            10000,
            10000,
            10000,
            10000
        ],
        [
            "'Жилое здание'",
            "'life_house'",
            5,
            1000,
            0,
            0
        ],
        [
            "'Амбар'",
            "'barn'",
            30,
            3000,
            0,
            0
        ],
        [
            "'Лесопилка'",
            "'sawmill'",
            30,
            3000,
            0,
            0
        ],
        [
            "'Каменоломня'",
            "'stonemason'",
            100,
            5000,
            5000,
            0
        ],
        [
            "'Плавильня'",
            "'smeltery'",
            500,
            10000,
            10000,
            10000
        ],
        [
            "'Библиотека'",
            "'library'",
            100,
            1000,
            1000,
            1000,
        ],
        [
            "'Гарнизон'",
            "'garrison'",
            500,
            3000,
            3000,
            3000,
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
            'INSERT IGNORE INTO build_type (name, code, gold, wood, stone, metal) VALUES '
            . implode(',', $values)
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE build_type');
    }
}

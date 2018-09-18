<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181007001401 extends AbstractMigration
{
    /** @var array */
    protected static $researchTypes = [
        [
            "'research_type_economy'",
            0,
            10000,
            10000,
            10000,
            10000,
        ],
    ];

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $values = [];
        $order = 1;
        foreach (self::$researchTypes as $buildType) {
            $buildType[] = $order++;
            $values[] = '(' . implode(',', $buildType) . ')';
        }

        $this->addSql(
            'INSERT IGNORE INTO research_type (`code`, `max_level`, `gold_cost`, `wood_cost`, `stone_cost`, `iron_cost`, `order`) VALUES '
            . implode(',', $values)
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE research_type');
    }
}

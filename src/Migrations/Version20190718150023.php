<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * @package Betreuteszocken\CsConfig
 */
final class Version20190718150023 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription() : string
    {
        return 'Initial set up';
    }

    /**
     * {@inheritDoc}
     *
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE map (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(63) NOT NULL, origin DATE DEFAULT NULL, `default` DATE DEFAULT NULL, removed DATE DEFAULT NULL, UNIQUE INDEX UNIQ_93ADAABB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(63) NOT NULL, regex VARCHAR(63) NOT NULL, `default` DATE DEFAULT NULL, UNIQUE INDEX UNIQ_64BE2FE65E237E06 (name), UNIQUE INDEX UNIQ_64BE2FE64204F8CA (regex), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cycle_config_id INT UNSIGNED DEFAULT NULL, mapcycle_txt LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B086D193F58602F3 (cycle_config_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_maps_mami (cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_39C063895EC1162 (cycle_id), INDEX IDX_39C0638953C55F64 (map_id), PRIMARY KEY(cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_maps_user (cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_F17328035EC1162 (cycle_id), INDEX IDX_F173280353C55F64 (map_id), PRIMARY KEY(cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_maps_default (cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_B46626FE5EC1162 (cycle_id), INDEX IDX_B46626FE53C55F64 (map_id), PRIMARY KEY(cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_maps_default_category (cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_209C07C45EC1162 (cycle_id), INDEX IDX_209C07C453C55F64 (map_id), PRIMARY KEY(cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_maps_origin (cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_8E62526A5EC1162 (cycle_id), INDEX IDX_8E62526A53C55F64 (map_id), PRIMARY KEY(cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_maps_random (cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_46A8DEA15EC1162 (cycle_id), INDEX IDX_46A8DEA153C55F64 (map_id), PRIMARY KEY(cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type SMALLINT NOT NULL COMMENT \'(DC2Type:log_type)\', message LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, deleted_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_config (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_maps SMALLINT UNSIGNED NOT NULL, default_maps SMALLINT UNSIGNED NOT NULL, default_category_maps SMALLINT UNSIGNED NOT NULL, origin_maps SMALLINT UNSIGNED NOT NULL, random_maps SMALLINT UNSIGNED NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cycle_config_mami_maps (map_cycle_id INT UNSIGNED NOT NULL, map_id INT UNSIGNED NOT NULL, INDEX IDX_6935EEA671E2745 (map_cycle_id), INDEX IDX_6935EEA53C55F64 (map_id), PRIMARY KEY(map_cycle_id, map_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cycle ADD CONSTRAINT FK_B086D193F58602F3 FOREIGN KEY (cycle_config_id) REFERENCES cycle_config (id)');
        $this->addSql('ALTER TABLE cycle_maps_mami ADD CONSTRAINT FK_39C063895EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE cycle_maps_mami ADD CONSTRAINT FK_39C0638953C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE cycle_maps_user ADD CONSTRAINT FK_F17328035EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE cycle_maps_user ADD CONSTRAINT FK_F173280353C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE cycle_maps_default ADD CONSTRAINT FK_B46626FE5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE cycle_maps_default ADD CONSTRAINT FK_B46626FE53C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE cycle_maps_default_category ADD CONSTRAINT FK_209C07C45EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE cycle_maps_default_category ADD CONSTRAINT FK_209C07C453C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE cycle_maps_origin ADD CONSTRAINT FK_8E62526A5EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE cycle_maps_origin ADD CONSTRAINT FK_8E62526A53C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE cycle_maps_random ADD CONSTRAINT FK_46A8DEA15EC1162 FOREIGN KEY (cycle_id) REFERENCES cycle (id)');
        $this->addSql('ALTER TABLE cycle_maps_random ADD CONSTRAINT FK_46A8DEA153C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE cycle_config_mami_maps ADD CONSTRAINT FK_6935EEA671E2745 FOREIGN KEY (map_cycle_id) REFERENCES cycle_config (id)');
        $this->addSql('ALTER TABLE cycle_config_mami_maps ADD CONSTRAINT FK_6935EEA53C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cycle_maps_mami DROP FOREIGN KEY FK_39C0638953C55F64');
        $this->addSql('ALTER TABLE cycle_maps_user DROP FOREIGN KEY FK_F173280353C55F64');
        $this->addSql('ALTER TABLE cycle_maps_default DROP FOREIGN KEY FK_B46626FE53C55F64');
        $this->addSql('ALTER TABLE cycle_maps_default_category DROP FOREIGN KEY FK_209C07C453C55F64');
        $this->addSql('ALTER TABLE cycle_maps_origin DROP FOREIGN KEY FK_8E62526A53C55F64');
        $this->addSql('ALTER TABLE cycle_maps_random DROP FOREIGN KEY FK_46A8DEA153C55F64');
        $this->addSql('ALTER TABLE cycle_config_mami_maps DROP FOREIGN KEY FK_6935EEA53C55F64');
        $this->addSql('ALTER TABLE cycle_maps_mami DROP FOREIGN KEY FK_39C063895EC1162');
        $this->addSql('ALTER TABLE cycle_maps_user DROP FOREIGN KEY FK_F17328035EC1162');
        $this->addSql('ALTER TABLE cycle_maps_default DROP FOREIGN KEY FK_B46626FE5EC1162');
        $this->addSql('ALTER TABLE cycle_maps_default_category DROP FOREIGN KEY FK_209C07C45EC1162');
        $this->addSql('ALTER TABLE cycle_maps_origin DROP FOREIGN KEY FK_8E62526A5EC1162');
        $this->addSql('ALTER TABLE cycle_maps_random DROP FOREIGN KEY FK_46A8DEA15EC1162');
        $this->addSql('ALTER TABLE cycle DROP FOREIGN KEY FK_B086D193F58602F3');
        $this->addSql('ALTER TABLE cycle_config_mami_maps DROP FOREIGN KEY FK_6935EEA671E2745');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE map_category');
        $this->addSql('DROP TABLE cycle');
        $this->addSql('DROP TABLE cycle_maps_mami');
        $this->addSql('DROP TABLE cycle_maps_user');
        $this->addSql('DROP TABLE cycle_maps_default');
        $this->addSql('DROP TABLE cycle_maps_default_category');
        $this->addSql('DROP TABLE cycle_maps_origin');
        $this->addSql('DROP TABLE cycle_maps_random');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE cycle_config');
        $this->addSql('DROP TABLE cycle_config_mami_maps');
    }
}
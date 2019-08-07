<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190726082207 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, spot_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, pseudo VARCHAR(255) DEFAULT NULL, mail VARCHAR(255) DEFAULT NULL, is_visible TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX IDX_67F068BC2DF1D37C (spot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE init_data_file (id INT AUTO_INCREMENT NOT NULL, data_file VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maree_maree (id INT AUTO_INCREMENT NOT NULL, state VARCHAR(5) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maree_restriction (id INT AUTO_INCREMENT NOT NULL, spot_id INT DEFAULT NULL, hauteur_max NUMERIC(5, 1) DEFAULT NULL, hauteur_min NUMERIC(5, 1) DEFAULT NULL, state VARCHAR(5) DEFAULT NULL, INDEX IDX_D51415DC2DF1D37C (spot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, num_display INT DEFAULT NULL, code_region VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_F62F17670E4A9D4 (code_region), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE spot (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, desc_route LONGTEXT DEFAULT NULL, desc_maree LONGTEXT DEFAULT NULL, time_from_paris INT DEFAULT NULL, km_from_paris INT DEFAULT NULL, km_autoroute_from_paris INT DEFAULT NULL, price_autoroute_from_paris NUMERIC(10, 2) DEFAULT NULL, gps_lat NUMERIC(10, 6) DEFAULT NULL, gps_long NUMERIC(10, 6) DEFAULT NULL, desc_orientation_vent LONGTEXT DEFAULT NULL, urlmap VARCHAR(255) DEFAULT NULL, url_wind_finder VARCHAR(512) DEFAULT NULL, urlwindguru VARCHAR(255) DEFAULT NULL, urlmeteo_france VARCHAR(255) DEFAULT NULL, urlmeteo_consult VARCHAR(255) DEFAULT NULL, urlmerteo VARCHAR(255) DEFAULT NULL, urlmaree VARCHAR(255) DEFAULT NULL, urltemp_water VARCHAR(255) DEFAULT NULL, hauteur_mbgrande_maree NUMERIC(5, 1) DEFAULT NULL, hauteur_mhgrande_maree NUMERIC(5, 1) DEFAULT NULL, hauteur_mbmoyenne_maree NUMERIC(5, 1) DEFAULT NULL, hauteur_mhmoyenne_maree NUMERIC(5, 1) DEFAULT NULL, hauteur_mbpetite_maree NUMERIC(5, 1) DEFAULT NULL, hauteur_mhpetite_maree NUMERIC(5, 1) DEFAULT NULL, url_balise VARCHAR(255) DEFAULT NULL, url_webcam VARCHAR(255) DEFAULT NULL, code_spot VARCHAR(10) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, is_foil TINYINT(1) DEFAULT NULL, desc_foil LONGTEXT DEFAULT NULL, is_contraint_ete TINYINT(1) DEFAULT NULL, desc_contraint_ete LONGTEXT DEFAULT NULL, desc_wave LONGTEXT DEFAULT NULL, INDEX IDX_B9327A7398260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_site_info (id INT AUTO_INCREMENT NOT NULL, spot_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, date DATETIME DEFAULT NULL, INDEX IDX_2EBF4D7A2DF1D37C (spot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wind_orientation (id INT AUTO_INCREMENT NOT NULL, spot_id INT DEFAULT NULL, orientation VARCHAR(20) DEFAULT NULL, orientation_deg NUMERIC(5, 1) DEFAULT NULL, state VARCHAR(5) DEFAULT NULL, INDEX IDX_608C89132DF1D37C (spot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC2DF1D37C FOREIGN KEY (spot_id) REFERENCES spot (id)');
        $this->addSql('ALTER TABLE maree_restriction ADD CONSTRAINT FK_D51415DC2DF1D37C FOREIGN KEY (spot_id) REFERENCES spot (id)');
        $this->addSql('ALTER TABLE spot ADD CONSTRAINT FK_B9327A7398260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE web_site_info ADD CONSTRAINT FK_2EBF4D7A2DF1D37C FOREIGN KEY (spot_id) REFERENCES spot (id)');
        $this->addSql('ALTER TABLE wind_orientation ADD CONSTRAINT FK_608C89132DF1D37C FOREIGN KEY (spot_id) REFERENCES spot (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spot DROP FOREIGN KEY FK_B9327A7398260155');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC2DF1D37C');
        $this->addSql('ALTER TABLE maree_restriction DROP FOREIGN KEY FK_D51415DC2DF1D37C');
        $this->addSql('ALTER TABLE web_site_info DROP FOREIGN KEY FK_2EBF4D7A2DF1D37C');
        $this->addSql('ALTER TABLE wind_orientation DROP FOREIGN KEY FK_608C89132DF1D37C');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE init_data_file');
        $this->addSql('DROP TABLE maree_maree');
        $this->addSql('DROP TABLE maree_restriction');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE spot');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE web_site_info');
        $this->addSql('DROP TABLE wind_orientation');
    }
}

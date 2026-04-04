<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401153951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availabilities (id INT AUTO_INCREMENT NOT NULL, availability INT NOT NULL, date DATE NOT NULL, fk_employee_id INT NOT NULL, INDEX IDX_D7FC41EFE5F55D68 (fk_employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EFE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
        $this->addSql('DROP TABLE availibilities');
        $this->addSql('ALTER TABLE interventions ADD CONSTRAINT FK_5ADBAD7FE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE interventions ADD CONSTRAINT FK_5ADBAD7F78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE used_pieces_interventions ADD CONSTRAINT FK_43B8D505791B1A5C FOREIGN KEY (used_pieces_id) REFERENCES used_pieces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE used_pieces_interventions ADD CONSTRAINT FK_43B8D505334423FF FOREIGN KEY (interventions_id) REFERENCES interventions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE used_pieces_pieces ADD CONSTRAINT FK_7A67116C791B1A5C FOREIGN KEY (used_pieces_id) REFERENCES used_pieces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE used_pieces_pieces ADD CONSTRAINT FK_7A67116C3FB89930 FOREIGN KEY (pieces_id) REFERENCES pieces (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availibilities (id INT AUTO_INCREMENT NOT NULL, availibility INT NOT NULL, date DATE NOT NULL, fk_employee_id INT DEFAULT NULL, INDEX IDX_6C24E3F7E5F55D68 (fk_employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EFE5F55D68');
        $this->addSql('DROP TABLE availabilities');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7FE5F55D68');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7F78B2BEB1');
        $this->addSql('ALTER TABLE used_pieces_interventions DROP FOREIGN KEY FK_43B8D505791B1A5C');
        $this->addSql('ALTER TABLE used_pieces_interventions DROP FOREIGN KEY FK_43B8D505334423FF');
        $this->addSql('ALTER TABLE used_pieces_pieces DROP FOREIGN KEY FK_7A67116C791B1A5C');
        $this->addSql('ALTER TABLE used_pieces_pieces DROP FOREIGN KEY FK_7A67116C3FB89930');
    }
}

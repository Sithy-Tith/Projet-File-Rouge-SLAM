<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409134058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE used_pieces_interventions');
        $this->addSql('DROP TABLE used_pieces_pieces');
        $this->addSql('ALTER TABLE availabilities CHANGE fk_employee_id fk_employee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EFE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE interventions ADD CONSTRAINT FK_5ADBAD7FE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE interventions ADD CONSTRAINT FK_5ADBAD7F78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE used_pieces ADD quantity DOUBLE PRECISION NOT NULL, ADD fk_intervention_id INT NOT NULL, ADD fk_piece_id INT NOT NULL');
        $this->addSql('ALTER TABLE used_pieces ADD CONSTRAINT FK_66AB57E3570D6C56 FOREIGN KEY (fk_intervention_id) REFERENCES interventions (id)');
        $this->addSql('ALTER TABLE used_pieces ADD CONSTRAINT FK_66AB57E3F6278C76 FOREIGN KEY (fk_piece_id) REFERENCES pieces (id)');
        $this->addSql('CREATE INDEX IDX_66AB57E3570D6C56 ON used_pieces (fk_intervention_id)');
        $this->addSql('CREATE INDEX IDX_66AB57E3F6278C76 ON used_pieces (fk_piece_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE used_pieces_interventions (used_pieces_id INT NOT NULL, interventions_id INT NOT NULL, INDEX IDX_43B8D505334423FF (interventions_id), INDEX IDX_43B8D505791B1A5C (used_pieces_id), PRIMARY KEY (used_pieces_id, interventions_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE used_pieces_pieces (used_pieces_id INT NOT NULL, pieces_id INT NOT NULL, INDEX IDX_7A67116C3FB89930 (pieces_id), INDEX IDX_7A67116C791B1A5C (used_pieces_id), PRIMARY KEY (used_pieces_id, pieces_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EFE5F55D68');
        $this->addSql('ALTER TABLE availabilities CHANGE fk_employee_id fk_employee_id INT NOT NULL');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7FE5F55D68');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7F78B2BEB1');
        $this->addSql('ALTER TABLE used_pieces DROP FOREIGN KEY FK_66AB57E3570D6C56');
        $this->addSql('ALTER TABLE used_pieces DROP FOREIGN KEY FK_66AB57E3F6278C76');
        $this->addSql('DROP INDEX IDX_66AB57E3570D6C56 ON used_pieces');
        $this->addSql('DROP INDEX IDX_66AB57E3F6278C76 ON used_pieces');
        $this->addSql('ALTER TABLE used_pieces DROP quantity, DROP fk_intervention_id, DROP fk_piece_id');
    }
}

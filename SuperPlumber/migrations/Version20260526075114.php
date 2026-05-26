<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260526075114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availabilities ADD all_day TINYINT NOT NULL');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EFE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE interventions DROP duration');
        $this->addSql('ALTER TABLE interventions ADD CONSTRAINT FK_5ADBAD7FE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE interventions ADD CONSTRAINT FK_5ADBAD7F78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE used_pieces ADD CONSTRAINT FK_66AB57E3570D6C56 FOREIGN KEY (fk_intervention_id) REFERENCES interventions (id)');
        $this->addSql('ALTER TABLE used_pieces ADD CONSTRAINT FK_66AB57E3F6278C76 FOREIGN KEY (fk_piece_id) REFERENCES pieces (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EFE5F55D68');
        $this->addSql('ALTER TABLE availabilities DROP all_day');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7FE5F55D68');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7F78B2BEB1');
        $this->addSql('ALTER TABLE interventions ADD duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE used_pieces DROP FOREIGN KEY FK_66AB57E3570D6C56');
        $this->addSql('ALTER TABLE used_pieces DROP FOREIGN KEY FK_66AB57E3F6278C76');
    }
}

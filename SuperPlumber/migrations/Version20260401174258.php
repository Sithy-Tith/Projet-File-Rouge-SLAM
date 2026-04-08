<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401174258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availabilities (id INT AUTO_INCREMENT NOT NULL, availability INT NOT NULL, date DATE NOT NULL, fk_employee_id INT DEFAULT NULL, INDEX IDX_D7FC41EFE5F55D68 (fk_employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(45) NOT NULL, first_name VARCHAR(45) NOT NULL, phone VARCHAR(45) NOT NULL, address VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE employees (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(45) NOT NULL, first_name VARCHAR(45) NOT NULL, phone VARCHAR(45) NOT NULL, position VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE interventions (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, duration INT NOT NULL, fk_employee_id INT DEFAULT NULL, fk_client_id INT DEFAULT NULL, INDEX IDX_5ADBAD7FE5F55D68 (fk_employee_id), INDEX IDX_5ADBAD7F78B2BEB1 (fk_client_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pieces (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, quantity INT NOT NULL, alert_treshold INT NOT NULL, supplier VARCHAR(45) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE used_pieces (id INT AUTO_INCREMENT NOT NULL, is_consumable TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE used_pieces_interventions (used_pieces_id INT NOT NULL, interventions_id INT NOT NULL, INDEX IDX_43B8D505791B1A5C (used_pieces_id), INDEX IDX_43B8D505334423FF (interventions_id), PRIMARY KEY (used_pieces_id, interventions_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE used_pieces_pieces (used_pieces_id INT NOT NULL, pieces_id INT NOT NULL, INDEX IDX_7A67116C791B1A5C (used_pieces_id), INDEX IDX_7A67116C3FB89930 (pieces_id), PRIMARY KEY (used_pieces_id, pieces_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE availabilities ADD CONSTRAINT FK_D7FC41EFE5F55D68 FOREIGN KEY (fk_employee_id) REFERENCES employees (id)');
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
        $this->addSql('ALTER TABLE availabilities DROP FOREIGN KEY FK_D7FC41EFE5F55D68');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7FE5F55D68');
        $this->addSql('ALTER TABLE interventions DROP FOREIGN KEY FK_5ADBAD7F78B2BEB1');
        $this->addSql('ALTER TABLE used_pieces_interventions DROP FOREIGN KEY FK_43B8D505791B1A5C');
        $this->addSql('ALTER TABLE used_pieces_interventions DROP FOREIGN KEY FK_43B8D505334423FF');
        $this->addSql('ALTER TABLE used_pieces_pieces DROP FOREIGN KEY FK_7A67116C791B1A5C');
        $this->addSql('ALTER TABLE used_pieces_pieces DROP FOREIGN KEY FK_7A67116C3FB89930');
        $this->addSql('DROP TABLE availabilities');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE employees');
        $this->addSql('DROP TABLE interventions');
        $this->addSql('DROP TABLE pieces');
        $this->addSql('DROP TABLE used_pieces');
        $this->addSql('DROP TABLE used_pieces_interventions');
        $this->addSql('DROP TABLE used_pieces_pieces');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

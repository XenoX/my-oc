<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200614174602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration, create all tables and constraints';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, start_at DATETIME NOT NULL, rate INT NOT NULL, no_show TINYINT(1) NOT NULL, INDEX IDX_1323A575166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, project_id INT NOT NULL, start_at DATETIME NOT NULL, duration VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', rate INT NOT NULL, evaluation TINYINT(1) NOT NULL, no_show TINYINT(1) NOT NULL, INDEX IDX_D044D5D4CB944F1A (student_id), INDEX IDX_D044D5D4166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE path (id INT AUTO_INCREMENT NOT NULL, id_oc INT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, duration VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', language VARCHAR(2) NOT NULL, link VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B548B0FB77AAC72 (id_oc), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE path_project (path_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_E805E6C7D96C566B (path_id), INDEX IDX_E805E6C7166D1F9C (project_id), PRIMARY KEY(path_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, path_id INT NOT NULL, project_id INT NOT NULL, id_oc INT NOT NULL, name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, funded TINYINT(1) NOT NULL, INDEX IDX_B723AF33D96C566B (path_id), INDEX IDX_B723AF33166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, id_oc INT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, duration VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', language VARCHAR(2) NOT NULL, evaluation VARCHAR(15) NOT NULL, rate INT NOT NULL, link VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2FB3D0EEB77AAC72 (id_oc), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE path_project ADD CONSTRAINT FK_E805E6C7D96C566B FOREIGN KEY (path_id) REFERENCES path (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE path_project ADD CONSTRAINT FK_E805E6C7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33D96C566B FOREIGN KEY (path_id) REFERENCES path (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE path_project DROP FOREIGN KEY FK_E805E6C7D96C566B');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33D96C566B');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4CB944F1A');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575166D1F9C');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4166D1F9C');
        $this->addSql('ALTER TABLE path_project DROP FOREIGN KEY FK_E805E6C7166D1F9C');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33166D1F9C');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE path');
        $this->addSql('DROP TABLE path_project');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE project');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200617172616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User system';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE path_project');
        $this->addSql('ALTER TABLE evaluation ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1323A575A76ED395 ON evaluation (user_id)');
        $this->addSql('ALTER TABLE session ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D4A76ED395 ON session (user_id)');
        $this->addSql('DROP INDEX UNIQ_B548B0FB77AAC72 ON path');
        $this->addSql('ALTER TABLE path ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE path ADD CONSTRAINT FK_B548B0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B548B0FA76ED395 ON path (user_id)');
        $this->addSql('ALTER TABLE student ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B723AF33A76ED395 ON student (user_id)');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EEB77AAC72 ON project');
        $this->addSql('ALTER TABLE project ADD path_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EED96C566B FOREIGN KEY (path_id) REFERENCES path (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EED96C566B ON project (path_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE path_project (path_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_E805E6C7D96C566B (path_id), INDEX IDX_E805E6C7166D1F9C (project_id), PRIMARY KEY(path_id, project_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE path_project ADD CONSTRAINT FK_E805E6C7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE path_project ADD CONSTRAINT FK_E805E6C7D96C566B FOREIGN KEY (path_id) REFERENCES path (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575A76ED395');
        $this->addSql('DROP INDEX IDX_1323A575A76ED395 ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP user_id');
        $this->addSql('ALTER TABLE path DROP FOREIGN KEY FK_B548B0FA76ED395');
        $this->addSql('DROP INDEX IDX_B548B0FA76ED395 ON path');
        $this->addSql('ALTER TABLE path DROP user_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B548B0FB77AAC72 ON path (id_oc)');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EED96C566B');
        $this->addSql('DROP INDEX IDX_2FB3D0EED96C566B ON project');
        $this->addSql('ALTER TABLE project DROP path_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EEB77AAC72 ON project (id_oc)');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4A76ED395');
        $this->addSql('DROP INDEX IDX_D044D5D4A76ED395 ON session');
        $this->addSql('ALTER TABLE session DROP user_id');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33A76ED395');
        $this->addSql('DROP INDEX IDX_B723AF33A76ED395 ON student');
        $this->addSql('ALTER TABLE student DROP user_id');
    }
}

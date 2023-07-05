<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630142733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AAF5D55E1');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AAF5D55E1AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11AF5D55E1');
        $this->addSql('ALTER TABLE participant ADD image_id INT DEFAULT NULL, CHANGE campus_id campus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B113DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B113DA5256D ON participant (image_id)');
        $this->addSql('ALTER TABLE venue DROP FOREIGN KEY FK_91911B0D8BAC62AF');
        $this->addSql('ALTER TABLE venue ADD CONSTRAINT FK_91911B0D8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, hashed_token VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AAF5D55E1AF5D55E1');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE venue DROP FOREIGN KEY FK_91911B0D8BAC62AF');
        $this->addSql('ALTER TABLE venue ADD CONSTRAINT FK_91911B0D8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B113DA5256D');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11AF5D55E1');
        $this->addSql('DROP INDEX UNIQ_D79F6B113DA5256D ON participant');
        $this->addSql('ALTER TABLE participant DROP image_id, CHANGE campus_id campus_id INT NOT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

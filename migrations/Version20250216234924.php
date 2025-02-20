<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216234924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE usuario_playlist (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_3F43E3B4DB38439E (usuario_id), INDEX IDX_3F43E3B46BBD148 (playlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usuario_playlist ADD CONSTRAINT FK_3F43E3B4DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario_playlist ADD CONSTRAINT FK_3F43E3B46BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario_playlist DROP FOREIGN KEY FK_3F43E3B4DB38439E');
        $this->addSql('ALTER TABLE usuario_playlist DROP FOREIGN KEY FK_3F43E3B46BBD148');
        $this->addSql('DROP TABLE usuario_playlist');
    }
}

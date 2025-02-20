<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214040216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cancion (id INT AUTO_INCREMENT NOT NULL, genero_id INT DEFAULT NULL, usuario_id INT DEFAULT NULL, titulo VARCHAR(255) NOT NULL, duracion INT NOT NULL, album VARCHAR(255) DEFAULT NULL, autor VARCHAR(255) NOT NULL, likes INT DEFAULT 0 NOT NULL, fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', año INT DEFAULT NULL, INDEX IDX_E4620FA0BCE7B795 (genero_id), INDEX IDX_E4620FA0DB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perfil_estilo (perfil_id INT NOT NULL, estilo_id INT NOT NULL, INDEX IDX_8C8A3EBE57291544 (perfil_id), INDEX IDX_8C8A3EBE43798DA7 (estilo_id), PRIMARY KEY(perfil_id, estilo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, propietario_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, visibilidad VARCHAR(255) NOT NULL, likes INT DEFAULT 0 NOT NULL, INDEX IDX_D782112D53C8D32C (propietario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_cancion (id INT AUTO_INCREMENT NOT NULL, playlist_id INT NOT NULL, cancion_id INT NOT NULL, reproducciones INT DEFAULT 0 NOT NULL, INDEX IDX_5B5D18BA6BBD148 (playlist_id), INDEX IDX_5B5D18BA9B1D840F (cancion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario_playlist_escuchadas (usuario_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_48ECFD1CDB38439E (usuario_id), INDEX IDX_48ECFD1C6BBD148 (playlist_id), PRIMARY KEY(usuario_id, playlist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario_cancion (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, cancion_id INT NOT NULL, reproducciones INT DEFAULT 0 NOT NULL, INDEX IDX_9D44A5E7DB38439E (usuario_id), INDEX IDX_9D44A5E79B1D840F (cancion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cancion ADD CONSTRAINT FK_E4620FA0BCE7B795 FOREIGN KEY (genero_id) REFERENCES estilo (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cancion ADD CONSTRAINT FK_E4620FA0DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE perfil_estilo ADD CONSTRAINT FK_8C8A3EBE57291544 FOREIGN KEY (perfil_id) REFERENCES perfil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE perfil_estilo ADD CONSTRAINT FK_8C8A3EBE43798DA7 FOREIGN KEY (estilo_id) REFERENCES estilo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112D53C8D32C FOREIGN KEY (propietario_id) REFERENCES usuario (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE playlist_cancion ADD CONSTRAINT FK_5B5D18BA6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_cancion ADD CONSTRAINT FK_5B5D18BA9B1D840F FOREIGN KEY (cancion_id) REFERENCES cancion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario_playlist_escuchadas ADD CONSTRAINT FK_48ECFD1CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario_playlist_escuchadas ADD CONSTRAINT FK_48ECFD1C6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario_cancion ADD CONSTRAINT FK_9D44A5E7DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE usuario_cancion ADD CONSTRAINT FK_9D44A5E79B1D840F FOREIGN KEY (cancion_id) REFERENCES cancion (id)');
        $this->addSql('ALTER TABLE perfil ADD usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE perfil ADD CONSTRAINT FK_96657647DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_96657647DB38439E ON perfil (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cancion DROP FOREIGN KEY FK_E4620FA0BCE7B795');
        $this->addSql('ALTER TABLE cancion DROP FOREIGN KEY FK_E4620FA0DB38439E');
        $this->addSql('ALTER TABLE perfil_estilo DROP FOREIGN KEY FK_8C8A3EBE57291544');
        $this->addSql('ALTER TABLE perfil_estilo DROP FOREIGN KEY FK_8C8A3EBE43798DA7');
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112D53C8D32C');
        $this->addSql('ALTER TABLE playlist_cancion DROP FOREIGN KEY FK_5B5D18BA6BBD148');
        $this->addSql('ALTER TABLE playlist_cancion DROP FOREIGN KEY FK_5B5D18BA9B1D840F');
        $this->addSql('ALTER TABLE usuario_playlist_escuchadas DROP FOREIGN KEY FK_48ECFD1CDB38439E');
        $this->addSql('ALTER TABLE usuario_playlist_escuchadas DROP FOREIGN KEY FK_48ECFD1C6BBD148');
        $this->addSql('ALTER TABLE usuario_cancion DROP FOREIGN KEY FK_9D44A5E7DB38439E');
        $this->addSql('ALTER TABLE usuario_cancion DROP FOREIGN KEY FK_9D44A5E79B1D840F');
        $this->addSql('DROP TABLE cancion');
        $this->addSql('DROP TABLE perfil_estilo');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_cancion');
        $this->addSql('DROP TABLE usuario_playlist_escuchadas');
        $this->addSql('DROP TABLE usuario_cancion');
        $this->addSql('ALTER TABLE perfil DROP FOREIGN KEY FK_96657647DB38439E');
        $this->addSql('DROP INDEX UNIQ_96657647DB38439E ON perfil');
        $this->addSql('ALTER TABLE perfil DROP usuario_id');
    }
}

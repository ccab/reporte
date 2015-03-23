<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migracion de la estructura debido al modulo UsersBundle
 * Se modifica la tabla servicios y areas, ademas se adicionan las tablas 
 * usuarios_reporte, roles_reporte y usuarios_roles
 */
class Version20141024194626 extends AbstractMigration
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
 

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('TRUNCATE TABLE servicios');
        $this->addSql('CREATE TABLE usuarios_reporte (id INT AUTO_INCREMENT NOT NULL, area_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, first_name VARCHAR(50) NOT NULL, full_name VARCHAR(50) NOT NULL, last_access DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_FD87704792FC23A8 (username_canonical), UNIQUE INDEX UNIQ_FD877047A0D96FBF (email_canonical), INDEX IDX_FD877047BD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuarios_roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_CE0972BAA76ED395 (user_id), INDEX IDX_CE0972BAD60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles_reporte (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, role VARCHAR(50) NOT NULL, nombre_grupo_dc VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_3F9564EF5E237E06 (name), UNIQUE INDEX UNIQ_3F9564EF57698A6A (role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usuarios_reporte ADD CONSTRAINT FK_FD877047BD0F409C FOREIGN KEY (area_id) REFERENCES areas (id)');
        $this->addSql('ALTER TABLE usuarios_roles ADD CONSTRAINT FK_CE0972BAA76ED395 FOREIGN KEY (user_id) REFERENCES usuarios_reporte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuarios_roles ADD CONSTRAINT FK_CE0972BAD60322AC FOREIGN KEY (role_id) REFERENCES roles_reporte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE areas ADD nombre_grupo_dc VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE servicios DROP FOREIGN KEY FK_C07E802F14D45BBE');
        $this->addSql('ALTER TABLE servicios ADD CONSTRAINT FK_C07E802F14D45BBE FOREIGN KEY (autor_id) REFERENCES usuarios_reporte (id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE servicios DROP FOREIGN KEY FK_C07E802F14D45BBE');
        $this->addSql('ALTER TABLE usuarios_roles DROP FOREIGN KEY FK_CE0972BAA76ED395');
        $this->addSql('ALTER TABLE usuarios_roles DROP FOREIGN KEY FK_CE0972BAD60322AC');
        $this->addSql('DROP TABLE usuarios_reporte');
        $this->addSql('DROP TABLE usuarios_roles');
        $this->addSql('DROP TABLE roles_reporte');
        $this->addSql('ALTER TABLE areas DROP nombre_grupo_dc');
        $this->addSql('ALTER TABLE servicios ADD CONSTRAINT FK_C07E802F14D45BBE FOREIGN KEY (autor_id) REFERENCES usuarios (id)');
    }
}

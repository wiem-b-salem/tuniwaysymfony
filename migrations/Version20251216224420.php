<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216224420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tourpersonnalise DROP FOREIGN KEY FKc7r1nopml87kvxu4gppldvnec');
        $this->addSql('ALTER TABLE tourpersonnalise DROP FOREIGN KEY FKtrtihla7vakacf87lvlhxfoqb');
        $this->addSql('DROP TABLE tourpersonnalise');
        $this->addSql('ALTER TABLE event CHANGE price price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CCDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE place ADD longtitude DOUBLE PRECISION DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD image_url VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP city, DROP imageUrl, DROP longitude, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE category category VARCHAR(50) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE name name VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FKt1xydejgjvsqay866jp9xsgkb');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FKs0oi69qdp6kore4nqa765gjef');
        $this->addSql('DROP INDEX FKs0oi69qdp6kore4nqa765gjef ON reservation');
        $this->addSql('DROP INDEX FKt1xydejgjvsqay866jp9xsgkb ON reservation');
        $this->addSql('ALTER TABLE reservation ADD tour_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD place_id INT DEFAULT NULL, ADD reservation_date DATETIME NOT NULL, ADD numbers_of_persons INT NOT NULL, ADD total_price DOUBLE PRECISION DEFAULT NULL, ADD created_at DATETIME NOT NULL, DROP client_id, DROP guide_id, DROP dateReservation, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE status status VARCHAR(50) NOT NULL, CHANGE type type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495515ED8D43 FOREIGN KEY (tour_id) REFERENCES tour_personnalise (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('CREATE INDEX IDX_42C8495515ED8D43 ON reservation (tour_id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C84955DA6A219 ON reservation (place_id)');
        $this->addSql('ALTER TABLE review ADD tour_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD status VARCHAR(20) NOT NULL, DROP datePosted, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE place_id place_id INT DEFAULT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE comment comment LONGTEXT DEFAULT NULL, CHANGE rating rating INT NOT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C615ED8D43 FOREIGN KEY (tour_id) REFERENCES tour_personnalise (id)');
        $this->addSql('CREATE INDEX IDX_794381C615ED8D43 ON review (tour_id)');
        $this->addSql('ALTER TABLE review RENAME INDEX fkhau0mes7q06rxfwq1rribokmb TO IDX_794381C6A76ED395');
        $this->addSql('ALTER TABLE review RENAME INDEX fkmh3uigcggh10c549li2dg8odf TO IDX_794381C6DA6A219');
        $this->addSql('ALTER TABLE tour_personnalise ADD CONSTRAINT FK_C51A082D7ED1D4B FOREIGN KEY (guide_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tour_personnalise ADD CONSTRAINT FK_C51A08219EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tour_personnalise_place ADD CONSTRAINT FK_A7D104B7369BD759 FOREIGN KEY (tour_personnalise_id) REFERENCES tour_personnalise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tour_personnalise_place ADD CONSTRAINT FK_A7D104B7DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD discr VARCHAR(255) NOT NULL, ADD preferences JSON DEFAULT NULL, DROP DTYPE, DROP profilePicture, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(50) NOT NULL, CHANGE username username VARCHAR(100) NOT NULL, CHANGE availability availability VARCHAR(50) DEFAULT NULL, CHANGE bio bio LONGTEXT DEFAULT NULL, CHANGE languages languages JSON DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tourpersonnalise (id BIGINT AUTO_INCREMENT NOT NULL, client_id BIGINT DEFAULT NULL, guide_id BIGINT DEFAULT NULL, date DATE DEFAULT \'NULL\', description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, prix DOUBLE PRECISION DEFAULT \'NULL\', titre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, INDEX FKtrtihla7vakacf87lvlhxfoqb (guide_id), INDEX FKc7r1nopml87kvxu4gppldvnec (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tourpersonnalise ADD CONSTRAINT FKc7r1nopml87kvxu4gppldvnec FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tourpersonnalise ADD CONSTRAINT FKtrtihla7vakacf87lvlhxfoqb FOREIGN KEY (guide_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event CHANGE price price DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CCA76ED395');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CCDA6A219');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE place ADD city VARCHAR(255) DEFAULT \'NULL\', ADD imageUrl VARCHAR(255) DEFAULT \'NULL\', ADD longitude DOUBLE PRECISION DEFAULT \'NULL\', DROP longtitude, DROP address, DROP image_url, DROP created_at, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE name name VARCHAR(255) DEFAULT \'NULL\', CHANGE description description VARCHAR(255) DEFAULT \'NULL\', CHANGE category category VARCHAR(255) DEFAULT \'NULL\', CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495515ED8D43');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955DA6A219');
        $this->addSql('DROP INDEX IDX_42C8495515ED8D43 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955DA6A219 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD client_id BIGINT DEFAULT NULL, ADD guide_id BIGINT DEFAULT NULL, ADD dateReservation DATETIME DEFAULT \'NULL\', DROP tour_id, DROP user_id, DROP place_id, DROP reservation_date, DROP numbers_of_persons, DROP total_price, DROP created_at, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE status status VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FKt1xydejgjvsqay866jp9xsgkb FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FKs0oi69qdp6kore4nqa765gjef FOREIGN KEY (guide_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX FKs0oi69qdp6kore4nqa765gjef ON reservation (guide_id)');
        $this->addSql('CREATE INDEX FKt1xydejgjvsqay866jp9xsgkb ON reservation (client_id)');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C615ED8D43');
        $this->addSql('DROP INDEX IDX_794381C615ED8D43 ON review');
        $this->addSql('ALTER TABLE review ADD datePosted DATETIME DEFAULT \'NULL\', DROP tour_id, DROP created_at, DROP status, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id BIGINT DEFAULT NULL, CHANGE place_id place_id BIGINT DEFAULT NULL, CHANGE rating rating INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE review RENAME INDEX idx_794381c6a76ed395 TO FKhau0mes7q06rxfwq1rribokmb');
        $this->addSql('ALTER TABLE review RENAME INDEX idx_794381c6da6a219 TO FKmh3uigcggh10c549li2dg8odf');
        $this->addSql('ALTER TABLE tour_personnalise DROP FOREIGN KEY FK_C51A082D7ED1D4B');
        $this->addSql('ALTER TABLE tour_personnalise DROP FOREIGN KEY FK_C51A08219EB6921');
        $this->addSql('ALTER TABLE tour_personnalise_place DROP FOREIGN KEY FK_A7D104B7369BD759');
        $this->addSql('ALTER TABLE tour_personnalise_place DROP FOREIGN KEY FK_A7D104B7DA6A219');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD DTYPE VARCHAR(31) NOT NULL, ADD profilePicture VARCHAR(255) DEFAULT \'NULL\', DROP roles, DROP discr, DROP preferences, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE email email VARCHAR(255) DEFAULT \'NULL\', CHANGE password password VARCHAR(255) DEFAULT \'NULL\', CHANGE username username VARCHAR(255) DEFAULT \'NULL\', CHANGE role role VARCHAR(255) DEFAULT \'NULL\', CHANGE bio bio VARCHAR(255) DEFAULT \'NULL\', CHANGE languages languages VARBINARY(255) DEFAULT \'NULL\', CHANGE availability availability VARCHAR(255) DEFAULT \'NULL\'');
    }
}

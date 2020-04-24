<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180817130113 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_refund_payment (id INT AUTO_INCREMENT NOT NULL, payment_method_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, order_number VARCHAR(255) NOT NULL, amount INT NOT NULL, currency_code VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_EFA5A4B25AA1164F (payment_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_refund_payment ADD CONSTRAINT FK_EFA5A4B25AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_refund_payment');
    }
}

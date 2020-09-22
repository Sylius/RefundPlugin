<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200125182414 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_refund_credit_memo_line_items (credit_memo_id VARCHAR(255) NOT NULL, line_item_id INT NOT NULL, INDEX IDX_1453B90E8E574316 (credit_memo_id), UNIQUE INDEX UNIQ_1453B90EA7CBD339 (line_item_id), PRIMARY KEY(credit_memo_id, line_item_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_refund_line_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, unit_net_price INT NOT NULL, unit_gross_price INT NOT NULL, net_value INT NOT NULL, gross_value INT NOT NULL, tax_amount INT NOT NULL, tax_rate VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo_line_items ADD CONSTRAINT FK_1453B90E8E574316 FOREIGN KEY (credit_memo_id) REFERENCES sylius_refund_credit_memo (id)');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo_line_items ADD CONSTRAINT FK_1453B90EA7CBD339 FOREIGN KEY (line_item_id) REFERENCES sylius_refund_line_item (id)');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo DROP units');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo_line_items DROP FOREIGN KEY FK_1453B90EA7CBD339');
        $this->addSql('DROP TABLE sylius_refund_credit_memo_line_items');
        $this->addSql('DROP TABLE sylius_refund_line_item');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD units LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:json)\'');
    }
}

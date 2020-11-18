<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200131082149 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_refund_credit_memo_tax_items (credit_memo_id VARCHAR(255) NOT NULL, tax_item_id INT NOT NULL, INDEX IDX_9BBDFBE28E574316 (credit_memo_id), UNIQUE INDEX UNIQ_9BBDFBE25327F254 (tax_item_id), PRIMARY KEY(credit_memo_id, tax_item_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_refund_tax_item (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo_tax_items ADD CONSTRAINT FK_9BBDFBE28E574316 FOREIGN KEY (credit_memo_id) REFERENCES sylius_refund_credit_memo (id)');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo_tax_items ADD CONSTRAINT FK_9BBDFBE25327F254 FOREIGN KEY (tax_item_id) REFERENCES sylius_refund_tax_item (id)');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo DROP tax_items');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo_tax_items DROP FOREIGN KEY FK_9BBDFBE25327F254');
        $this->addSql('DROP TABLE sylius_refund_credit_memo_tax_items');
        $this->addSql('DROP TABLE sylius_refund_tax_item');
        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD tax_items LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:json)\'');
    }
}

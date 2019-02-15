<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190215134758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo ADD from_customerName VARCHAR(255) NOT NULL, ADD from_street VARCHAR(255) NOT NULL, ADD from_postcode VARCHAR(255) NOT NULL, ADD from_countryCode VARCHAR(255) NOT NULL, ADD from_city VARCHAR(255) NOT NULL, ADD from_company VARCHAR(255) DEFAULT NULL, ADD from_provinceName VARCHAR(255) DEFAULT NULL, ADD from_provinceCode VARCHAR(255) DEFAULT NULL, ADD to_company VARCHAR(255) NOT NULL, ADD to_taxId VARCHAR(255) NOT NULL, ADD to_countryCode VARCHAR(255) NOT NULL, ADD to_street VARCHAR(255) NOT NULL, ADD to_city VARCHAR(255) NOT NULL, ADD to_postcode VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_refund_credit_memo DROP from_customerName, DROP from_street, DROP from_postcode, DROP from_countryCode, DROP from_city, DROP from_company, DROP from_provinceName, DROP from_provinceCode, DROP to_company, DROP to_taxId, DROP to_countryCode, DROP to_street, DROP to_city, DROP to_postcode');
    }
}

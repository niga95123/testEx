<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325145926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
                CREATE TABLE public.tech_country_phone_number (
                    id BIGSERIAL NOT NULL,
                    country_name VARCHAR(255) NOT NULL,
                    phone_num_code INT NOT NULL,
                    PRIMARY KEY(id)
                )');
        $this->addSql('
                CREATE TABLE public.tech_guest (
                    id BIGSERIAL NOT NULL,
                    country_phone_number_id BIGINT NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    surname VARCHAR(255) NOT NULL,
                    phone_number BIGSERIAL NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    PRIMARY KEY(id)
                )');
        $this->addSql('CREATE INDEX IDX_DE92E2F1FE94872F ON public.tech_guest (country_phone_number_id)');
        $this->addSql('ALTER TABLE public.tech_guest ADD CONSTRAINT FK_DE92E2F1FE94872F FOREIGN KEY (country_phone_number_id) REFERENCES public.tech_country_phone_number (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE public.tech_guest DROP CONSTRAINT FK_DE92E2F1FE94872F');
        $this->addSql('DROP TABLE public.tech_country_phone_number');
        $this->addSql('DROP TABLE public.tech_guest');
    }
}

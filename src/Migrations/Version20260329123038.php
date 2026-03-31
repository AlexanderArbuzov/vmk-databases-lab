<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260329123038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS products (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                sku INTEGER UNIQUE CHECK (sku >= 10000 AND sku <= 99999),
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10, 2) NOT NULL CHECK (price > 0)
            );
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS orders (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                total_sum DECIMAL(10, 2) NOT NULL DEFAULT 0
            );
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS order_items (
                id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
                sku INTEGER NOT NULL REFERENCES products(sku) ON DELETE RESTRICT ON UPDATE CASCADE,
                quantity INTEGER NOT NULL CHECK (quantity > 0),
                PRIMARY KEY (id, sku)
            );
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS education_degrees (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                name VARCHAR(255) NOT NULL,
                mapping_key INTEGER UNIQUE NOT NULL
            );
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS roles (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                name VARCHAR(255) NOT NULL,
                education_degree_mapping_key INTEGER REFERENCES education_degrees(mapping_key) ON DELETE RESTRICT NOT NULL,
                is_adult BOOLEAN NOT NULL
            );
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS staff (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                full_name VARCHAR(255) NOT NULL,
                birthday DATE NOT NULL,
                education_degree_id INTEGER REFERENCES education_degrees(id) ON DELETE RESTRICT NOT NULL
            );
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS staff_roles (
                employee_id BIGINT REFERENCES staff(id) ON DELETE CASCADE NOT NULL,
                role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE NOT NULL,
                PRIMARY KEY (employee_id, role_id)
            );
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS product_roles (
                role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE NOT NULL,
                product_id BIGINT REFERENCES products(id) ON DELETE CASCADE NOT NULL,
                PRIMARY KEY (role_id, product_id)
            );
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS product_roles;');
        $this->addSql('DROP TABLE IF EXISTS staff_roles;');
        $this->addSql('DROP TABLE IF EXISTS order_items;');

        $this->addSql('DROP TABLE IF EXISTS staff;');
        $this->addSql('DROP TABLE IF EXISTS roles;');

        $this->addSql('DROP TABLE IF EXISTS education_degrees;');
        $this->addSql('DROP TABLE IF EXISTS orders;');
        $this->addSql('DROP TABLE IF EXISTS products;');
    }
}

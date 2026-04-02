<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260330090116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE FUNCTION update_order_total_sum() RETURNS trigger
            AS $$
            BEGIN
                UPDATE orders
                SET total_sum = total_sum + NEW.quantity
                                                * (SELECT price FROM products WHERE sku = NEW.sku)
                WHERE id = NEW.id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL
        );

        $this->addSql(<<<'SQL'
            CREATE TRIGGER trg_order_items_insert
            AFTER INSERT ON order_items
            FOR EACH ROW
            EXECUTE FUNCTION update_order_total_sum();
        SQL
        );

        $this->addSql(<<<'SQL'
            CREATE FUNCTION check_staff_role_req() RETURNS trigger
            AS $$
            DECLARE
                emp_birthday DATE;
                emp_edu_degree INTEGER;
                role_is_adult BOOLEAN;
                role_min_edu_degree INTEGER;
                emp_edu_degree_name VARCHAR(255);
                role_min_edu_degree_name VARCHAR(255);
            BEGIN
                SELECT s.birthday, ed.mapping_key, ed.name
                INTO emp_birthday, emp_edu_degree, emp_edu_degree_name
                FROM staff s
                INNER JOIN education_degrees ed ON s.education_degree_mapping_key = ed.mapping_key
                WHERE s.id = NEW.employee_id;

                SELECT r.is_adult, ed.mapping_key, ed.name
                INTO role_is_adult, role_min_edu_degree, role_min_edu_degree_name
                FROM roles r
                INNER JOIN education_degrees ed ON r.education_degree_mapping_key = ed.mapping_key
                WHERE r.id = NEW.role_id;
            
                IF role_is_adult AND emp_birthday > (CURRENT_DATE - INTERVAL '18 years') THEN
                   RAISE EXCEPTION 'Выбранная должность доступна только лицам достигшим возраста совершеннолетия.';
                END IF;
            
                IF emp_edu_degree < role_min_edu_degree THEN
                   RAISE EXCEPTION 'Уровень образования кандидата (%) не соответствует выбранной должности (%).', 
                   emp_edu_degree_name, role_min_edu_degree_name;
                END IF;
            
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL
        );

        $this->addSql(<<<'SQL'
            CREATE TRIGGER trg_before_insert_staff_role
            BEFORE INSERT OR UPDATE ON staff_roles
            FOR EACH ROW
            EXECUTE FUNCTION check_staff_role_req();
        SQL);
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS trg_order_items_insert ON order_items;');
        $this->addSql('DROP FUNCTION IF EXISTS update_order_total_sum();');
        $this->addSql('DROP TRIGGER IF EXISTS trg_before_insert_staff_role ON staff_roles;');
        $this->addSql('DROP FUNCTION IF EXISTS check_staff_role_req();');
    }
}

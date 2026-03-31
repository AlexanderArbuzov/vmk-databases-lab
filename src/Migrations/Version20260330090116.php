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
            CREATE FUNCTION updateOrderTotalSum() RETURNS trigger
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
            EXECUTE FUNCTION updateOrderTotalSum();
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
            BEGIN
                SELECT s.birthday, ed.mapping
                INTO emp_birthday, emp_edu_degree
                FROM staff s
                JOIN education_degrees ed ON s.education_degree = ed.id
                WHERE s.id = NEW.employee_id;

                SELECT r.is_adult, ed.mapping
                INTO role_is_adult, role_min_edu_degree
                FROM roles r
                JOIN education_degrees ed ON r.education_degree_mapping = ed.id
                WHERE r.id = NEW.role_id;
            
                IF role_is_adult AND emp_birthday > (CURRENT_DATE - INTERVAL '18 years') THEN
                   RAISE EXCEPTION 'ОШИБКА: Роль требует 18+, а сотруднику меньше.';
                END IF;
            
                IF emp_edu_rank < role_min_edu_degree THEN
                   RAISE EXCEPTION 'ОШИБКА: Уровень образования сотрудника (ранг %) ниже требуемого (ранг %).', 
                   emp_edu_rank, role_min_edu_rank;
                END IF;
            
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL
        );

        $this->addSql(<<<'SQL'
            CREATE TRIGGER trg_before_insert_staff_role
            BEFORE INSERT OR UPDATE ON staff_roles
            FOR EACH ROW EXECUTE FUNCTION check_staff_role_req();
        SQL);
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS trg_order_items_insert ON order_items;');
        $this->addSql('DROP FUNCTION IF EXISTS updateOrderTotalSum();');
        $this->addSql('DROP TRIGGER IF EXISTS trg_before_insert_staff_role ON staff_roles;');
        $this->addSql('DROP FUNCTION IF EXISTS check_staff_role_req();');
    }
}

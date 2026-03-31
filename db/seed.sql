INSERT INTO products (sku, name, price) VALUES (10001, 'Futurama Space Ship', 1999000);
INSERT INTO products (sku, name, price) VALUES (10002, '1981 DMC DeLorean (by Dr. Emmett Brown)', 3799000);
INSERT INTO products (sku, name, price) VALUES (10003, '"Roadside Picnic", Arkady and Boris Strugatsky, 1977', 100);

INSERT INTO education_degrees (name, mapping_key) VALUES ('Higher ed.', 3);
INSERT INTO education_degrees (name, mapping_key) VALUES ('College', 2);
INSERT INTO education_degrees (name, mapping_key) VALUES ('High school', 1);
INSERT INTO education_degrees (name, mapping_key) VALUES ('Uneducated', 0);

INSERT INTO roles (name, education_degree_mapping_key, is_adult) VALUES ('Store Manager', 3, true);
INSERT INTO roles (name, education_degree_mapping_key, is_adult) VALUES ('Accountant', 2, true);
INSERT INTO roles (name, education_degree_mapping_key, is_adult) VALUES ('Cashier', 2, true);
INSERT INTO roles (name, education_degree_mapping_key, is_adult) VALUES ('Mover', 0, true);
INSERT INTO roles (name, education_degree_mapping_key, is_adult) VALUES ('Shop Assistant', 0, false);

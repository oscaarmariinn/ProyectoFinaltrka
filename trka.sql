SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- USERS (15)
-- =========================
INSERT INTO user (id, email, roles, password, name, surname, created_at, active) VALUES
                                                                                     (1,'user1@mail.com','["ROLE_USER"]','hashed_pass','Juan','Pérez',NOW(),1),
                                                                                     (2,'user2@mail.com','["ROLE_USER"]','hashed_pass','Ana','López',NOW(),1),
                                                                                     (3,'user3@mail.com','["ROLE_USER"]','hashed_pass','Carlos','Gómez',NOW(),1),
                                                                                     (4,'user4@mail.com','["ROLE_USER"]','hashed_pass','Laura','Martín',NOW(),1),
                                                                                     (5,'user5@mail.com','["ROLE_USER"]','hashed_pass','Pedro','Sánchez',NOW(),1),
                                                                                     (6,'user6@mail.com','["ROLE_USER"]','hashed_pass','Lucía','Romero',NOW(),1),
                                                                                     (7,'user7@mail.com','["ROLE_USER"]','hashed_pass','Mario','Díaz',NOW(),1),
                                                                                     (8,'user8@mail.com','["ROLE_USER"]','hashed_pass','Sara','Vega',NOW(),1),
                                                                                     (9,'user9@mail.com','["ROLE_USER"]','hashed_pass','David','Ruiz',NOW(),1),
                                                                                     (10,'user10@mail.com','["ROLE_USER"]','hashed_pass','Elena','Navarro',NOW(),1),
                                                                                     (11,'user11@mail.com','["ROLE_USER"]','hashed_pass','Pablo','Ortega',NOW(),1),
                                                                                     (12,'user12@mail.com','["ROLE_USER"]','hashed_pass','Marta','Cano',NOW(),1),
                                                                                     (13,'user13@mail.com','["ROLE_USER"]','hashed_pass','Jorge','Molina',NOW(),1),
                                                                                     (14,'user14@mail.com','["ROLE_USER"]','hashed_pass','Nuria','Castro',NOW(),1),
                                                                                     (15,'user15@mail.com','["ROLE_USER"]','hashed_pass','Iván','Flores',NOW(),1);

-- =========================
-- GROUPS (15)
-- =========================
INSERT INTO `group` (id, name, description, created_at, is_private, creator_id) VALUES
                                                                                    (1,'Grupo 1','Descripción grupo 1',NOW(),0,1),
                                                                                    (2,'Grupo 2','Descripción grupo 2',NOW(),1,2),
                                                                                    (3,'Grupo 3','Descripción grupo 3',NOW(),0,3),
                                                                                    (4,'Grupo 4','Descripción grupo 4',NOW(),1,4),
                                                                                    (5,'Grupo 5','Descripción grupo 5',NOW(),0,5),
                                                                                    (6,'Grupo 6','Descripción grupo 6',NOW(),1,6),
                                                                                    (7,'Grupo 7','Descripción grupo 7',NOW(),0,7),
                                                                                    (8,'Grupo 8','Descripción grupo 8',NOW(),1,8),
                                                                                    (9,'Grupo 9','Descripción grupo 9',NOW(),0,9),
                                                                                    (10,'Grupo 10','Descripción grupo 10',NOW(),1,10),
                                                                                    (11,'Grupo 11','Descripción grupo 11',NOW(),0,11),
                                                                                    (12,'Grupo 12','Descripción grupo 12',NOW(),1,12),
                                                                                    (13,'Grupo 13','Descripción grupo 13',NOW(),0,13),
                                                                                    (14,'Grupo 14','Descripción grupo 14',NOW(),1,14),
                                                                                    (15,'Grupo 15','Descripción grupo 15',NOW(),0,15);

-- =========================
-- CATEGORIES (15)
-- =========================
INSERT INTO category (id, name) VALUES
                                    (1,'Comida'),
                                    (2,'Cena'),
                                    (3,'Almuerzo'),
                                    (4,'Tardeo'),
                                    (5,'Fiesta'),
                                    (6,'Cine'),
                                    (7,'Centro Comercial'),
                                    (8,'Tomar algo'),
                                    (9,'Deporte'),
                                    (10,'Cumpleaños');


-- =========================
-- EVENTS (15)
-- =========================
INSERT INTO event (
    id, title, description, event_date, location, max_participants,
    is_public, created_at, event_group_id, creator_id, is_verified
) VALUES
      (1,'Evento 1','Desc evento 1',NOW(),'Madrid',50,1,NOW(),1,1,1),
      (2,'Evento 2','Desc evento 2',NOW(),'Barcelona',40,0,NOW(),2,2,1),
      (3,'Evento 3','Desc evento 3',NOW(),'Valencia',30,1,NOW(),3,3,0),
      (4,'Evento 4','Desc evento 4',NOW(),'Sevilla',20,0,NOW(),4,4,1),
      (5,'Evento 5','Desc evento 5',NOW(),'Bilbao',25,1,NOW(),5,5,1),
      (6,'Evento 6','Desc evento 6',NOW(),'Zaragoza',60,0,NOW(),6,6,0),
      (7,'Evento 7','Desc evento 7',NOW(),'Málaga',70,1,NOW(),7,7,1),
      (8,'Evento 8','Desc evento 8',NOW(),'Murcia',80,0,NOW(),8,8,1),
      (9,'Evento 9','Desc evento 9',NOW(),'Granada',90,1,NOW(),9,9,0),
      (10,'Evento 10','Desc evento 10',NOW(),'Cádiz',35,0,NOW(),10,10,1),
      (11,'Evento 11','Desc evento 11',NOW(),'León',45,1,NOW(),11,11,1),
      (12,'Evento 12','Desc evento 12',NOW(),'Oviedo',55,0,NOW(),12,12,0),
      (13,'Evento 13','Desc evento 13',NOW(),'Santander',65,1,NOW(),13,13,1),
      (14,'Evento 14','Desc evento 14',NOW(),'Burgos',75,0,NOW(),14,14,1),
      (15,'Evento 15','Desc evento 15',NOW(),'Toledo',85,1,NOW(),15,15,1);

-- =========================
-- USER ↔ GROUP
-- =========================
INSERT INTO user_group (user_id, group_id) VALUES
                                               (1,1),(2,1),(3,2),(4,2),(5,3),
                                               (6,3),(7,4),(8,4),(9,5),(10,5),
                                               (11,6),(12,7),(13,8),(14,9),(15,10);

-- =========================
-- USER ↔ EVENT
-- =========================
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1,1),(2,1),(3,2),(4,2),(5,3),
                                               (6,4),(7,5),(8,6),(9,7),(10,8),
                                               (11,9),(12,10),(13,11),(14,12),(15,13);

-- =========================
-- EVENT ↔ CATEGORY
-- =========================
INSERT INTO event_category (event_id, category_id) VALUES
                                                       (1,1),(1,2),
                                                       (2,3),(2,4),
                                                       (3,5),
                                                       (4,6),
                                                       (5,7),
                                                       (6,8),
                                                       (7,9),
                                                       (8,10),
                                                       (9,6),
                                                       (10,5),
                                                       (11,7),
                                                       (12,9),
                                                       (13,4);

SET FOREIGN_KEY_CHECKS = 1;

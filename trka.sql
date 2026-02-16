-- ============================================
-- SQL Script para Aplicación Web de Eventos
-- ============================================

-- Limpiar tablas en orden correcto (respetando foreign keys)
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE user_event;
TRUNCATE TABLE user_group;
TRUNCATE TABLE event_category;
TRUNCATE TABLE event;
TRUNCATE TABLE `group`;
TRUNCATE TABLE category;
TRUNCATE TABLE user;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- INSERTAR CATEGORÍAS
-- ============================================
INSERT INTO category (id, name) VALUES
                                    (1, 'Comida'),
                                    (2, 'Cena'),
                                    (3, 'Almuerzo'),
                                    (4, 'Tardeo'),
                                    (5, 'Fiesta'),
                                    (6, 'Cine'),
                                    (7, 'Centro Comercial'),
                                    (8, 'Tomar algo'),
                                    (9, 'Deporte'),
                                    (10, 'Cumpleaños');

-- ============================================
-- INSERTAR USUARIOS
-- ============================================
INSERT INTO user (id, email, roles, password, name, surname, created_at, active) VALUES
                                                                                     (1, 'admin@gmail.com', '[\"ROLE_SUPERADMIN\"]', '$2y$13$yVFX5kpapswBFjIsQK.ahOpSVZGpff0DnAwgB/CytqVHcOTh5xA4i', 'Oscar', 'Marin', '2026-02-11 11:38:33', 1),
                                                                                     (4, 'sergi@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$CbTMTwgdjxHxV1VhlKt/QOsl3NiOXdodsiQcNX0JJHWlgf4Z1Lg.G', 'Sergi', 'Company', '2026-02-12 10:45:12', 1),
                                                                                     (5, 'santino@gmail.com', '[\"ROLE_USER\"]', '$2y$13$QhYKUn60jntmSw1sAC3rsuhDcOHzEON0tAX2snxaPezYF6qkAp5fK', 'Santino', 'Alico', '2026-02-12 10:45:58', 1),
                                                                                     (6, 'davidmeprime@gmail.com', '[\"ROLE_USER\"]', '$2y$13$byuOIRHE0Y4YELmyHzHas.ICRds8/AjQTJIKWDHqQyk3lKmFJn5Pe', 'David', 'me', '2026-02-16 09:34:22', 1);

-- ============================================
-- INSERTAR GRUPOS
-- ============================================
INSERT INTO `group` (id, name, description, created_at, is_private, creator_id) VALUES
                                                                                    (1, 'Amigos de la Universidad', 'Grupo de antiguos compañeros de universidad que quedan regularmente', '2026-01-15 10:00:00', 0, 1),
                                                                                    (2, 'Foodie Lovers', 'Para los que disfrutan descubriendo nuevos restaurantes y experiencias gastronómicas', '2026-01-20 14:30:00', 0, 4),
                                                                                    (3, 'Deportistas del Fin de Semana', 'Quedadas deportivas los fines de semana: running, padel, futbol', '2026-01-25 09:00:00', 1, 5),
                                                                                    (4, 'Cinéfilos Sabadell', 'Grupo privado para ir al cine y comentar películas', '2026-02-01 18:00:00', 1, 6),
                                                                                    (5, 'Fiesteros Catalans', 'Para organizar salidas nocturnas y eventos de ocio', '2026-02-05 20:00:00', 0, 1);

-- ============================================
-- INSERTAR RELACIÓN USUARIOS-GRUPOS
-- ============================================
-- Grupo 1: Amigos de la Universidad (Oscar, Sergi, Santino)
INSERT INTO user_group (user_id, group_id) VALUES
                                               (1, 1),
                                               (4, 1),
                                               (5, 1);

-- Grupo 2: Foodie Lovers (Sergi, David, Oscar)
INSERT INTO user_group (user_id, group_id) VALUES
                                               (4, 2),
                                               (6, 2),
                                               (1, 2);

-- Grupo 3: Deportistas del Fin de Semana (Santino, Oscar, Sergi)
INSERT INTO user_group (user_id, group_id) VALUES
                                               (5, 3),
                                               (1, 3),
                                               (4, 3);

-- Grupo 4: Cinéfilos Sabadell (David, Santino, Oscar)
INSERT INTO user_group (user_id, group_id) VALUES
                                               (6, 4),
                                               (5, 4),
                                               (1, 4);

-- Grupo 5: Fiesteros Catalans (Todos los usuarios)
INSERT INTO user_group (user_id, group_id) VALUES
                                               (1, 5),
                                               (4, 5),
                                               (5, 5),
                                               (6, 5);

-- ============================================
-- INSERTAR EVENTOS
-- ============================================

-- Eventos del Grupo 1: Amigos de la Universidad
INSERT INTO event (id, title, description, event_date, location, max_participants, is_public, created_at, is_verified, event_group_id, creator_id) VALUES
                                                                                                                                                       (1, 'Cena de Reencuentro', 'Cena para ponernos al día después de tanto tiempo. Reserva en Restaurante La Tagliatella.', '2026-02-25 21:00:00', 'La Tagliatella, Sabadell', 8, 0, '2026-02-16 10:00:00', 1, 1, 1),
                                                                                                                                                       (2, 'Tarde de Juegos de Mesa', 'Quedada para jugar al Catan y otros juegos en casa de Sergi', '2026-02-28 17:00:00', 'Casa de Sergi, Sabadell', 6, 0, '2026-02-16 10:15:00', 1, 1, 4),
                                                                                                                                                       (3, 'Cumpleaños de Oscar', 'Celebración del cumpleaños de Oscar en local privado', '2026-03-10 19:00:00', 'Bowling Sabadell', 15, 0, '2026-02-16 10:30:00', 1, 1, 1);

-- Eventos del Grupo 2: Foodie Lovers
INSERT INTO event (id, title, description, event_date, location, max_participants, is_public, created_at, is_verified, event_group_id, creator_id) VALUES
                                                                                                                                                       (4, 'Ruta de Tapas en Gracia', 'Descubrimos los mejores bares de tapas del barrio de Gracia', '2026-02-22 13:00:00', 'Barrio de Gracia, Barcelona', 10, 1, '2026-02-16 11:00:00', 1, 2, 4),
                                                                                                                                                       (5, 'Clase de Cocina Japonesa', 'Taller para aprender a hacer sushi y ramen casero', '2026-03-05 18:30:00', 'Escuela de Cocina Tokyo, Barcelona', 12, 1, '2026-02-16 11:15:00', 1, 2, 6),
                                                                                                                                                       (6, 'Brunch Dominical', 'Brunch en el nuevo local de moda con vistas al mar', '2026-03-09 11:00:00', 'Brunch & Cake, Barcelona', 8, 1, '2026-02-16 11:30:00', 1, 2, 4);

-- Eventos del Grupo 3: Deportistas del Fin de Semana
INSERT INTO event (id, title, description, event_date, location, max_participants, is_public, created_at, is_verified, event_group_id, creator_id) VALUES
                                                                                                                                                       (7, 'Partido de Padel', 'Dobles de padel. Nivel intermedio. Traer raqueta propia.', '2026-02-21 10:00:00', 'Club Deportivo Sabadell', 4, 0, '2026-02-16 12:00:00', 1, 3, 5),
                                                                                                                                                       (8, 'Running Matinal 10K', 'Ruta de 10km por el Parc Catalunya. Ritmo 5:30 min/km', '2026-02-23 08:00:00', 'Parc Catalunya, Sabadell', 15, 0, '2026-02-16 12:15:00', 1, 3, 1),
                                                                                                                                                       (9, 'Futbol 7 - Amistoso', 'Partido amistoso de futbol 7. Necesitamos 14 jugadores.', '2026-03-01 18:00:00', 'Campo Municipal Sabadell', 14, 0, '2026-02-16 12:30:00', 1, 3, 4);

-- Eventos del Grupo 4: Cinéfilos Sabadell
INSERT INTO event (id, title, description, event_date, location, max_participants, is_public, created_at, is_verified, event_group_id, creator_id) VALUES
                                                                                                                                                       (10, 'Dune: Part Three - Estreno', 'Vamos a ver el estreno de la nueva película de Dune', '2026-02-27 22:00:00', 'Cines Yelmo, Sabadell', 10, 0, '2026-02-16 13:00:00', 1, 4, 6),
                                                                                                                                                       (11, 'Cine de Autor - Ciclo Almodóvar', 'Sesión especial de cine español con debate posterior', '2026-03-06 19:30:00', 'Filmoteca de Catalunya', 8, 0, '2026-02-16 13:15:00', 1, 4, 5),
                                                                                                                                                       (12, 'Maratón Marvel en Casa', 'Vemos las 3 películas del Universo Marvel en casa con palomitas', '2026-03-08 15:00:00', 'Casa de David, Sabadell', 6, 0, '2026-02-16 13:30:00', 1, 4, 6);

-- Eventos del Grupo 5: Fiesteros Catalans
INSERT INTO event (id, title, description, event_date, location, max_participants, is_public, created_at, is_verified, event_group_id, creator_id) VALUES
                                                                                                                                                       (13, 'Tardeo en la Playa', 'Tardeo con DJ en chiringuito de la Barceloneta', '2026-02-20 18:00:00', 'Chiringuito Escribà, Barcelona', 20, 1, '2026-02-16 14:00:00', 1, 5, 1),
                                                                                                                                                       (14, 'Fiesta Carnaval', 'Gran fiesta de Carnaval con disfraces obligatorios', '2026-02-28 23:00:00', 'Sala Apolo, Barcelona', 30, 1, '2026-02-16 14:15:00', 1, 5, 4),
                                                                                                                                                       (15, 'After Work Viernes', 'Copas después del trabajo para empezar el fin de semana', '2026-02-27 19:00:00', 'Bar Marsella, Barcelona', 15, 1, '2026-02-16 14:30:00', 1, 5, 5);

-- ============================================
-- INSERTAR CATEGORÍAS DE EVENTOS
-- ============================================

-- Evento 1: Cena de Reencuentro
INSERT INTO event_category (event_id, category_id) VALUES (1, 2); -- Cena

-- Evento 2: Tarde de Juegos de Mesa
INSERT INTO event_category (event_id, category_id) VALUES (2, 8); -- Tomar algo

-- Evento 3: Cumpleaños de Oscar
INSERT INTO event_category (event_id, category_id) VALUES (3, 10); -- Cumpleaños
INSERT INTO event_category (event_id, category_id) VALUES (3, 5); -- Fiesta

-- Evento 4: Ruta de Tapas
INSERT INTO event_category (event_id, category_id) VALUES (4, 1); -- Comida
INSERT INTO event_category (event_id, category_id) VALUES (4, 8); -- Tomar algo

-- Evento 5: Clase de Cocina Japonesa
INSERT INTO event_category (event_id, category_id) VALUES (5, 1); -- Comida

-- Evento 6: Brunch Dominical
INSERT INTO event_category (event_id, category_id) VALUES (6, 3); -- Almuerzo

-- Evento 7: Partido de Padel
INSERT INTO event_category (event_id, category_id) VALUES (7, 9); -- Deporte

-- Evento 8: Running Matinal
INSERT INTO event_category (event_id, category_id) VALUES (8, 9); -- Deporte

-- Evento 9: Futbol 7
INSERT INTO event_category (event_id, category_id) VALUES (9, 9); -- Deporte

-- Evento 10: Dune Part Three
INSERT INTO event_category (event_id, category_id) VALUES (10, 6); -- Cine

-- Evento 11: Cine de Autor
INSERT INTO event_category (event_id, category_id) VALUES (11, 6); -- Cine

-- Evento 12: Maratón Marvel
INSERT INTO event_category (event_id, category_id) VALUES (12, 6); -- Cine

-- Evento 13: Tardeo en la Playa
INSERT INTO event_category (event_id, category_id) VALUES (13, 4); -- Tardeo
INSERT INTO event_category (event_id, category_id) VALUES (13, 8); -- Tomar algo

-- Evento 14: Fiesta Carnaval
INSERT INTO event_category (event_id, category_id) VALUES (14, 5); -- Fiesta

-- Evento 15: After Work Viernes
INSERT INTO event_category (event_id, category_id) VALUES (15, 8); -- Tomar algo
INSERT INTO event_category (event_id, category_id) VALUES (15, 4); -- Tardeo

-- ============================================
-- INSERTAR ASISTENCIAS A EVENTOS (user_event)
-- ============================================

-- Evento 1: Cena de Reencuentro (Grupo 1)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1, 1),
                                               (4, 1),
                                               (5, 1);

-- Evento 2: Tarde de Juegos (Grupo 1)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1, 2),
                                               (4, 2);

-- Evento 3: Cumpleaños de Oscar (Grupo 1)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1, 3),
                                               (4, 3),
                                               (5, 3),
                                               (6, 3);

-- Evento 4: Ruta de Tapas (Grupo 2)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (4, 4),
                                               (6, 4),
                                               (1, 4),
                                               (5, 4);

-- Evento 5: Clase de Cocina (Grupo 2)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (4, 5),
                                               (6, 5),
                                               (1, 5);

-- Evento 6: Brunch Dominical (Grupo 2)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (4, 6),
                                               (6, 6);

-- Evento 7: Partido de Padel (Grupo 3)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (5, 7),
                                               (1, 7),
                                               (4, 7);

-- Evento 8: Running Matinal (Grupo 3)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (5, 8),
                                               (1, 8),
                                               (4, 8);

-- Evento 9: Futbol 7 (Grupo 3)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (5, 9),
                                               (1, 9),
                                               (4, 9),
                                               (6, 9);

-- Evento 10: Dune Part Three (Grupo 4)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (6, 10),
                                               (5, 10),
                                               (1, 10);

-- Evento 11: Cine de Autor (Grupo 4)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (6, 11),
                                               (5, 11);

-- Evento 12: Maratón Marvel (Grupo 4)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (6, 12),
                                               (5, 12),
                                               (1, 12);

-- Evento 13: Tardeo en la Playa (Grupo 5)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1, 13),
                                               (4, 13),
                                               (5, 13),
                                               (6, 13);

-- Evento 14: Fiesta Carnaval (Grupo 5)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1, 14),
                                               (4, 14),
                                               (5, 14),
                                               (6, 14);

-- Evento 15: After Work Viernes (Grupo 5)
INSERT INTO user_event (user_id, event_id) VALUES
                                               (1, 15),
                                               (4, 15),
                                               (6, 15);

-- ============================================
-- RESETEAR AUTO_INCREMENT (Opcional)
-- ============================================
ALTER TABLE category AUTO_INCREMENT = 11;
ALTER TABLE user AUTO_INCREMENT = 7;
ALTER TABLE `group` AUTO_INCREMENT = 6;
ALTER TABLE event AUTO_INCREMENT = 16;

-- ============================================
-- VERIFICACIÓN DE DATOS
-- ============================================

-- Mostrar resumen de datos insertados
SELECT 'CATEGORÍAS INSERTADAS:' AS '';
SELECT COUNT(*) AS total_categorias FROM category;

SELECT 'USUARIOS INSERTADOS:' AS '';
SELECT COUNT(*) AS total_usuarios FROM user;

SELECT 'GRUPOS CREADOS:' AS '';
SELECT COUNT(*) AS total_grupos FROM `group`;

SELECT 'EVENTOS CREADOS:' AS '';
SELECT COUNT(*) AS total_eventos FROM event;

SELECT 'RELACIONES USUARIO-GRUPO:' AS '';
SELECT COUNT(*) AS total_relaciones FROM user_group;

SELECT 'RELACIONES USUARIO-EVENTO:' AS '';
SELECT COUNT(*) AS total_asistencias FROM user_event;

SELECT 'RELACIONES EVENTO-CATEGORÍA:' AS '';
SELECT COUNT(*) AS total_categorias_evento FROM event_category;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
-- Limpiamos las tablas para evitar duplicados
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `group_user`;
TRUNCATE TABLE `user_group`;
TRUNCATE TABLE `user_event`;
TRUNCATE TABLE `event_category`;
TRUNCATE TABLE `event`;
TRUNCATE TABLE `group`;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `category`;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert categories orientadas a ocio juvenil (5)
INSERT INTO `category` (`name`) VALUES
                                    ('Fiestas y Nightlife'),
                                    ('Deportes y Aventura'),
                                    ('Videojuegos y Gaming'),
                                    ('Música y Conciertos'),
                                    ('Street Food y Gastronomía');

-- Insert users (10) - perfiles jóvenes
INSERT INTO `user` (`email`, `roles`, `password`, `name`, `surname`, `created_at`, `active`) VALUES
                                                                                                 ('alex.garcia@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567890', 'Álex', 'García', '2025-01-01 10:00:00', 1),
                                                                                                 ('lucia.martinez@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567891', 'Lucía', 'Martínez', '2025-01-01 10:05:00', 1),
                                                                                                 ('carlos.lopez@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567892', 'Carlos', 'López', '2025-01-01 10:10:00', 1),
                                                                                                 ('maria.sanchez@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567893', 'María', 'Sánchez', '2025-01-01 10:15:00', 1),
                                                                                                 ('david.fernandez@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567894', 'David', 'Fernández', '2025-01-01 10:20:00', 1),
                                                                                                 ('ana.gonzalez@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567895', 'Ana', 'González', '2025-01-01 10:25:00', 1),
                                                                                                 ('javi.ruiz@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567896', 'Javi', 'Ruiz', '2025-01-01 10:30:00', 1),
                                                                                                 ('clara.torres@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567897', 'Clara', 'Torres', '2025-01-01 10:35:00', 1),
                                                                                                 ('pablo.navarro@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567898', 'Pablo', 'Navarro', '2025-01-01 10:40:00', 1),
                                                                                                 ('laura.diaz@email.com', '["ROLE_USER"]', '$2y$13$abcdefghijklmnopqrstuvwxyz1234567899', 'Laura', 'Díaz', '2025-01-01 10:45:00', 1);

-- Insert grupos juveniles (3)
INSERT INTO `group` (`name`, `description`, `created_at`, `is_private`, `creator_id`) VALUES
                                                                                          ('Party Animals', 'El grupo más fiestero. Salidas a discotecas y festivales', '2025-01-02 10:00:00', 0, 1),
                                                                                          ('Skater Crew', 'Quedadas para patinar y hacer deporte extremo', '2025-01-03 10:00:00', 1, 3),
                                                                                          ('Gamers United', 'Torneos de videojuegos y quedadas para jugar', '2025-01-04 10:00:00', 0, 5);

-- Insert eventos juveniles (5)
INSERT INTO `event` (`title`, `description`, `event_date`, `location`, `max_participants`, `is_public`, `created_at`, `event_group_id`, `creator_id`, `is_verified`) VALUES
                                                                                                                                                                         ('Festival Sun & Beats', 'Festival de música electrónica con los mejores DJs. Edición verano 2025', '2025-07-15 20:00:00', 'Playa de la Barceloneta', 5000, 1, '2025-01-05 10:00:00', 1, 1, 1),
                                                                                                                                                                         ('Torneo FIFA 24', 'Competición de FIFA 24 con premios y streaming', '2025-06-22 16:00:00', 'Gaming Center', 64, 1, '2025-01-06 10:00:00', 3, 5, 1),
                                                                                                                                                                         ('Night Urban Fest', 'Conciertos de trap y rap emergente', '2025-07-01 21:00:00', 'Sala Apolo', 800, 1, '2025-01-07 10:00:00', NULL, 2, 1),
                                                                                                                                                                         ('Beach Volley Tournament', 'Torneo de vóley playa con after party incluida', '2025-06-28 10:00:00', 'Playa de la Victoria', 128, 0, '2025-01-08 10:00:00', 2, 3, 0),
                                                                                                                                                                         ('Food Trucks Night', 'Noche de food trucks con música y cerveza artesana', '2025-07-10 19:00:00', 'Puerto Deportivo', 2000, 1, '2025-01-09 10:00:00', NULL, 4, 1);

-- Insert event_category (relación eventos-categorías)
INSERT INTO `event_category` (`event_id`, `category_id`) VALUES
                                                             (1, 4), (1, 1),  -- Festival Sun & Beats: Música y Fiestas
                                                             (2, 3),           -- Torneo FIFA: Videojuegos
                                                             (3, 4), (3, 1),  -- Night Urban Fest: Música y Fiestas
                                                             (4, 2), (4, 1),  -- Beach Volley: Deportes y Fiestas
                                                             (5, 5), (5, 1);  -- Food Trucks: Gastronomía y Fiestas

-- Insert user_event (jóvenes apuntándose a eventos)
INSERT INTO `user_event` (`user_id`, `event_id`) VALUES
                                                     (1, 1), (1, 3), (1, 5),  -- Álex: Festival, Urban Fest, Food Trucks
                                                     (2, 1), (2, 3), (2, 5),  -- Lucía: Festival, Urban Fest, Food Trucks
                                                     (3, 4), (3, 2),          -- Carlos: Beach Volley, FIFA
                                                     (4, 1), (4, 3), (4, 5),  -- María: Festival, Urban Fest, Food Trucks
                                                     (5, 2), (5, 3),          -- David: FIFA, Urban Fest
                                                     (6, 1), (6, 4), (6, 5),  -- Ana: Festival, Beach Volley, Food Trucks
                                                     (7, 3), (7, 2), (7, 4),  -- Javi: Urban Fest, FIFA, Beach Volley
                                                     (8, 1), (8, 3), (8, 5),  -- Clara: Festival, Urban Fest, Food Trucks
                                                     (9, 2), (9, 4),          -- Pablo: FIFA, Beach Volley
                                                     (10, 1), (10, 3), (10, 5); -- Laura: Festival, Urban Fest, Food Trucks

-- Insert user_group (pertenencia a grupos)
INSERT INTO `user_group` (`user_id`, `group_id`) VALUES
                                                     (1, 1), (1, 3),  -- Álex: Party Animals, Gamers
                                                     (2, 1),          -- Lucía: Party Animals
                                                     (3, 2), (3, 3),  -- Carlos: Skater Crew, Gamers
                                                     (4, 1),          -- María: Party Animals
                                                     (5, 3),          -- David: Gamers
                                                     (6, 2),          -- Ana: Skater Crew
                                                     (7, 2), (7, 3),  -- Javi: Skater Crew, Gamers
                                                     (8, 1), (8, 2),  -- Clara: Party Animals, Skater Crew
                                                     (9, 3),          -- Pablo: Gamers
                                                     (10, 1), (10, 2); -- Laura: Party Animals, Skater Crew

-- Insert group_user (responsables/organizadores)
INSERT INTO `group_user` (`group_id`, `user_id`) VALUES
                                                     (1, 1), (1, 2),  -- Party Animals organizado por Álex y Lucía
                                                     (2, 3), (2, 6),  -- Skater Crew organizado por Carlos y Ana
                                                     (3, 5), (3, 7);  -- Gamers United organizado por David y Javi
-- 🔄 Réinitialisation complète
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS users;

-- 👤 Table des utilisateurs
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(100) NOT NULL UNIQUE,
                       password TEXT NOT NULL,
                       role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ✅ Table des tâches
CREATE TABLE tasks (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       user_id INT NOT NULL,
                       description TEXT NOT NULL,
                       is_done BOOLEAN DEFAULT FALSE,
                       deleted BOOLEAN DEFAULT FALSE,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP NULL DEFAULT NULL,
                       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 🔐 Mots de passe Bcrypt
-- admin123 → $2y$10$RpJAjBnAAVhDnHbG/9QqF.6k7sn/NmR5YkbpnMH03KcNBTe0M7zAm
-- etudiant123 → $2y$10$6duRZIvDc2rL4HPl5IXtkeLxN9uPU4mjR6eqHLHo7bmj1ftmLmpKa

-- 👮 Admin
INSERT INTO users (username, password, role) VALUES
    ('admin', '$2y$10$zy5J7G5qsVR0vbdrG4PlUOP2wDeRq.QRtolzpm2PXUEOYCOMhid/G', 'admin');

-- 🔄 Génération des 59 étudiants
SET @pwd := '$2y$10$9kV8qSGJuizUnTdv0WMR4uM7KG7aNckAaERF.Sr.4lS1QgQeB.3HO';

INSERT INTO users (username, password)
SELECT CONCAT('etudiant', LPAD(n, 2, '0')), @pwd
FROM (
         SELECT @row := @row + 1 AS n
         FROM (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
               UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
              (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
               UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
              (SELECT @row := 0) r
     ) numbers
WHERE n BETWEEN 1 AND 59;

-- 📚 50 tâches uniques
CREATE TEMPORARY TABLE sample_tasks (description TEXT);
INSERT INTO sample_tasks (description) VALUES
                                           ('Finish HTML/CSS project'),
                                           ('Study for the Java exam'),
                                           ('Submit weekly progress report'),
                                           ('Fix bugs in PHP project'),
                                           ('Push code to GitHub'),
                                           ('Prepare for Spring Boot workshop'),
                                           ('Read chapter 4 of Clean Code'),
                                           ('Update LinkedIn profile'),
                                           ('Review SQL joins'),
                                           ('Deploy app on XAMPP'),
                                           ('Complete group project task'),
                                           ('Install MariaDB locally'),
                                           ('Participate in code review'),
                                           ('Watch PHP security tutorial'),
                                           ('Add comments to source code'),
                                           ('Write documentation'),
                                           ('Refactor the ToDoApp'),
                                           ('Test login feature'),
                                           ('Resolve merge conflicts'),
                                           ('Meet mentor for project feedback'),
                                           ('Create ERD for database'),
                                           ('Practice Docker basics'),
                                           ('Implement login validation'),
                                           ('Design homepage layout'),
                                           ('Create user story map'),
                                           ('Finish UML class diagram'),
                                           ('Write PHPUnit tests'),
                                           ('Document API endpoints'),
                                           ('Fix navbar responsiveness'),
                                           ('Optimize SQL queries'),
                                           ('Update README.md'),
                                           ('Try out Laravel framework'),
                                           ('Write shell script for backup'),
                                           ('Clean old project folders'),
                                           ('Review pull requests'),
                                           ('Configure .htaccess rules'),
                                           ('Check .env security'),
                                           ('Prepare presentation slides'),
                                           ('Conduct peer code review'),
                                           ('Implement search filter'),
                                           ('Use AJAX for live search'),
                                           ('Organize tasks by priority'),
                                           ('Schedule mock interviews'),
                                           ('Build resume with LaTeX'),
                                           ('Learn Tailwind CSS'),
                                           ('Create portfolio project'),
                                           ('Practice with Git branching'),
                                           ('Create markdown cheatsheet'),
                                           ('Compare REST vs GraphQL'),
                                           ('Enable dark mode toggle');

-- ⚙️ Génération aléatoire des tâches
DELIMITER $$

CREATE PROCEDURE generate_tasks()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE uid INT;
    DECLARE cur CURSOR FOR SELECT id FROM users WHERE role = 'user';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO uid;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET @task_count = FLOOR(RAND() * 31); -- 0 à 30 tâches

        WHILE @task_count > 0 DO
                INSERT INTO tasks (user_id, description, is_done, deleted, created_at, updated_at)
                SELECT uid,
                       (SELECT description FROM sample_tasks ORDER BY RAND() LIMIT 1),
                       (RAND() < 0.5),  -- 50 % de chances "faite"
                       FALSE,
                       DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY),
                       NULL;
                SET @task_count = @task_count - 1;
            END WHILE;

    END LOOP;

    CLOSE cur;
END $$

DELIMITER ;

-- 🔁 Exécute la procédure
CALL generate_tasks();

-- 🧹 Nettoyage
DROP PROCEDURE generate_tasks;
DROP TEMPORARY TABLE sample_tasks;
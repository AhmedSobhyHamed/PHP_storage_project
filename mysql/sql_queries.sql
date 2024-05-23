

-- from digitalocean create user , default authentication is caching_sha2_password
-- first line is for preview the command
-- second line for more secure but not for external connection as with root user 
CREATE USER 'username'@'host' IDENTIFIED WITH authentication_plugin BY 'password';
ALTER USER 'sammy'@'localhost' IDENTIFIED WITH auth_socket BY 'password';
CREATE USER 'sammy'@'localhost' IDENTIFIED BY 'password';
ALTER USER 'sammy'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
CREATE USER 'sammy'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
-- from digitalocean grant privilages
-- first line is for preview the command
GRANT PRIVILEGE ON database.table TO 'username'@'host';
GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT, REFERENCES, RELOAD 
on *.* TO 'sammy'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'sammy'@'localhost' WITH GRANT OPTION;
-- from digitalocean flush or reload privilages after change any grant or create user 
-- spesialy with changed dirctely from the table
FLUSH PRIVILEGES;
-- from digitalocean revoke or delete privilages
REVOKE type_of_permission ON database_name.table_name FROM 'username'@'host';
-- from digitalocean debug-> show privilages for user
SHOW GRANTS FOR 'username'@'host';
-- from digitalocean delete user
DROP USER 'username'@'localhost';






-- create database
CREATE DATABASE `card_site`;
USE `card_site`;
-- create tables
CREATE TABLE IF NOT EXISTS `users`(
    `name` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs DEFAULT NULL,
    `email` VARCHAR(128) NOT NULL DEFAULT '',
    `password` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs DEFAULT NULL,
    `photo` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs DEFAULT NULL,
    `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`),
    UNIQUE `email` (`email`)
) ENGINE InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci AUTO_INCREMENT=56232;

CREATE TABLE IF NOT EXISTS `media`(
    `name` VARCHAR(128) DEFAULT NULL,
    `weburl` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs NOT NULL DEFAULT '',
    `localurl` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs DEFAULT NULL 
    COMMENT 'if there is anther source in ur local disk',
    `tags` VARCHAR(128) NOT NULL DEFAULT '',
    `img` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs DEFAULT NULL 
    COMMENT 'if the media was image then this is a sample or small image',
    `id` SMALLINT NOT NULL AUTO_INCREMENT,
    `user` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `date` DATETIME NOT NULL DEFAULT now(),
    PRIMARY key (`id`),
    UNIQUE `web` (`weburl`),
    FULLTEXT INDEX `name` (`name`),
    FULLTEXT INDEX `tags` (`tags`),
    FULLTEXT INDEX `media` (`name`,`tags`),
    FOREIGN KEY `user` (`user`) REFERENCES `card_site`.`users`(`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `manga`(
    `name` VARCHAR(128) DEFAULT NULL,
    `url` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs NOT NULL DEFAULT '',
    `img` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs DEFAULT NULL ,
    `description` VARCHAR(1024) NOT NULL DEFAULT '',
    `chapter` SMALLINT NOT NULL DEFAULT 0,
    `id` SMALLINT NOT NULL AUTO_INCREMENT,
    `user` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `date` DATETIME NOT NULL DEFAULT now(),
    PRIMARY key (`id`),
    UNIQUE `web` (`url`),
    FULLTEXT INDEX `name` (`name`),
    FULLTEXT INDEX `dscrp` (`description`),
    FULLTEXT INDEX `manga` (`name`,`description`),
    FOREIGN KEY `user` (`user`) REFERENCES `card_site`.`users`(`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `notes`(
    `title` VARCHAR(64) DEFAULT NULL,
    `id` SMALLINT NOT NULL AUTO_INCREMENT,
    `user` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `date` DATETIME NOT NULL DEFAULT now(),
    PRIMARY key (`id`),
    FULLTEXT INDEX `title` (`title`),
    FOREIGN KEY `user` (`user`) REFERENCES `card_site`.`users`(`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `snippet`(
    `text` VARCHAR(2048) DEFAULT NULL,
    `note` SMALLINT NOT NULL DEFAULT 0,
    `id` SMALLINT NOT NULL AUTO_INCREMENT,
    PRIMARY key (`id`),
    FOREIGN KEY `noteID` (`note`) REFERENCES `card_site`.`notes`(`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci 
COMMENT 'note id allways follow his parent column in updates and delete';
-- create user
CREATE USER IF NOT EXISTS
'cardsite'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'OOU000123'
REQUIRE NONE
WITH MAX_CONNECTIONS_PER_HOUR 1800 MAX_USER_CONNECTIONS 5
PASSWORD EXPIRE NEVER FAILED_LOGIN_ATTEMPTS 5 PASSWORD REQUIRE CURRENT PASSWORD_LOCK_TIME 1;
-- grant privilages
GRANT INSERT,UPDATE,DELETE,SELECT ON `card\_site`.* TO 'cardsite'@'localhost';
FLUSH PRIVILEGES;
-- queries
START TRANSACTION;
COMMIT;
-- create new user
INSERT INTO `users`(`name`,`email`,`password`,`photo`) VALUES('----','-----','-----',DEFAULT);
-- create new note with title
INSERT INTO `notes`(`title`,`user`)VALUES('-------','USER ID');
-- add a text to note 
INSERT INTO `snippet`(`text`,`note`) VALUES('-----','NOTE ID');
IF('NOTE ID' IN (SELECT `id` FROM `notes` WHERE `user`='USER ID'),INSERT INTO `snippet`(`text`,`note`) VALUES('-----','NOTE ID')); 
INSERT INTO `snippet`(`text`,`note`) SELECT '-----' AS `text`,'NOTE ID' AS `note` WHERE 'NOTE ID' IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- delete text from the note
DELETE FROM `snippet` WHERE `id`='SNIPPET ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- edit text from note
UPDATE `snippet` SET `text`='--------' WHERE `id`='SNIPPET ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- delete the note
-- DELETE FROM `snippet` WHERE `note`='NOTE ID' AND IF('NOTE ID' IN (SELECT `id` FROM `notes` WHERE `user`='USER ID'));
-- check clause ON DELETE CASCADE if it not work the delete rows from snippet table first
DELETE FROM `notes` WHERE `id`='NOTE ID' AND `user`='USER ID';
-- view note details
SELECT `title` AS 'name',`date` FROM `notes` WHERE `id`='NOTE ID' AND `user`='USER ID';
-- view all text in the note
SELECT `text`,`id` FROM `snippet` WHERE `note`='NOTE ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- view all notes sorted by date limited from n to n asc
SELECT `title`,`id`,`date` FROM `notes` WHERE `user`='USER ID' ORDER BY `date` ASC LIMIT N,N;
-- view all notes sorted by date limited from n to n desc
SELECT `title`,`id`,`date` FROM `notes` WHERE `user`='USER ID' ORDER BY `date` DESC LIMIT N,N;
-- view all notes sorted by name limited from n to n
SELECT `title`,`id`,`date` FROM `notes` WHERE `user`='USER ID' ORDER BY `title` ASC LIMIT N,N;

-- create a new manga with title and all other things
INSERT INTO `manga`(`name`,`url`,`img`,`description`,`chapter`,`user`)VALUES('--------',DEFAULT,DEFAULT,DEFAULT,DEFAULT,'USER ID');
-- change URL
UPDATE `manga` SET `url`='-------' WHERE `id`='MANGA ID' AND `user`='USER ID';
-- change image
UPDATE `manga` SET `img`='-------' WHERE `id`='MANGA ID' AND `user`='USER ID';
-- change description
UPDATE `manga` SET `description`='-------' WHERE `id`='MANGA ID' AND `user`='USER ID';
-- change last read chapter and update date
UPDATE `manga` SET `chapter`='-------' , `date`=DEFAULT WHERE `id`='MANGA ID' AND `user`='USER ID';
-- delete manga
DELETE FROM `manga` WHERE `id`='MANGA ID' AND `user`='USER ID';
-- view manga details
SELECT `name`,`url`,`img`,`description`,`chapter`,`date` FROM `manga` WHERE `id`='MANGA ID' AND `user`='USER ID';
-- view all manga sorted by date  limited from n to n asc
SELECT `name`,`img`,`description`,`chapter`,`id`,`date` FROM `manga` WHERE `user`='USER ID' ORDER BY `date` ASC LIMIT N,N;
-- view all manga sorted by date  limited from n to n desc
SELECT `name`,`img`,`description`,`chapter`,`id`,`date` FROM `manga` WHERE `user`='USER ID' ORDER BY `date` DESC LIMIT N,N;
-- view all manga sorted by name  limited from n to n
SELECT `name`,`img`,`description`,`chapter`,`id`,`date` FROM `manga` WHERE `user`='USER ID' ORDER BY `name` ASC LIMIT N,N;

-- create media with name , URL, and other things and add date 
INSERT INTO `media`(`name`,`weburl`,`localurl`,`tags`,`img`,`user`)VALUES('--------','-------',DEFAULT,DEFAULT,DEFAULT,'USER ID');
-- add local URL
-- edit local URL
-- delete local URL
UPDATE `media` SET `localurl`='-------' WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- change URL
UPDATE `media` SET `weburl`='-------' WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- change image
-- add image
-- delete image
UPDATE `media` SET `img`='-------' WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- add tag
UPDATE `media` SET `tags`=CONCAT(`tags`,'{','-------','}') WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- delete tag
UPDATE `media` SET `tags`=REPLACE(`tags`,CONCAT('{','-------','}'),'') WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- delete all tags
UPDATE `media` SET `tags`=DEFAULT WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- delete media
DELETE FROM `media` WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- view media
SELECT `name`,`weburl`,`localurl`,`tags`,`img`,`date` FROM `media` WHERE `id`='MEDIA ID' AND `user`='USER ID';
-- view all media sorted by date limited from n to n asc
SELECT `name`,`tags`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID' ORDER BY `date` ASC LIMIT N,N;
-- view all media sorted by date limited from n to n desc
SELECT `name`,`tags`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID' ORDER BY `date` DESC LIMIT N,N;
-- view all media sorted by name limited from n to n
SELECT `name`,`tags`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID' ORDER BY `name` ASC LIMIT N,N;

-- view all media, notes, manga sorted by date limited from n to n acs
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID') 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id` FROM `manga` WHERE `user`='USER ID') 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID') 
ORDER BY `date` ASC LIMIT N,N;
-- view all media, notes, manga sorted by date limited from n to n desc
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID') 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id` FROM `manga` WHERE `user`='USER ID') 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID') 
ORDER BY `date` DESC LIMIT N,N;
-- view all media, notes, manga sorted by name limited from n to n
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID') 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id` FROM `manga` WHERE `user`='USER ID') 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID') 
ORDER BY `name` ASC LIMIT N,N;
-- view all media, notes, manga that matchs 'text' sorted by date limited from n to n acs
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID' AND MATCH(`name`,`tags`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id` FROM `manga` WHERE `user`='USER ID' AND MATCH(`name`,`description`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID' AND MATCH(`title`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
ORDER BY `date` ASC LIMIT N,N;
-- view all media, notes, manga that matchs 'text' sorted by date limited from n to n desc
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID' AND MATCH(`name`,`description`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id` FROM `manga` WHERE `user`='USER ID' AND MATCH(`name`,`description`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID' AND MATCH(`title`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
ORDER BY `date` DESC LIMIT N,N;
-- view all media, notes, manga that matchs 'text' sorted by name limited from n to n
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id` FROM `media` WHERE `user`='USER ID' AND MATCH(`name`,`tags`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id` FROM `manga` WHERE `user`='USER ID' AND MATCH(`name`,`description`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID' AND MATCH(`title`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
ORDER BY `name` ASC LIMIT N,N;
-- view all matchs for search in any fulltext field 
(SELECT `name`,`tags` AS `description`,`img`,`date`,`id`, MATCH(`name`,`tags`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE) AS `SEO` FROM `media` WHERE `user`='USER ID' AND MATCH(`name`,`tags`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `name`,`description`,`img`,`date`,`id`, MATCH(`name`,`description`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE) AS `SEO` FROM `manga` WHERE `user`='USER ID' AND MATCH(`name`,`description`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
UNION ALL 
(SELECT `title` AS `name`,'NOTE TEXT' AS `description`,'NO IMAGE' AS `img`,`date`,`id`, MATCH(`title`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE) AS `SEO` FROM `notes` WHERE `user`='USER ID' AND MATCH(`title`) AGAINST(_utf8mb4 '--------' COLLATE utf8mb4_0900_as_ci IN BOOLEAN MODE)) 
ORDER BY `SEO` ASC LIMIT N,N;








-- SELECT `title` AS `name`,(SELECT `text` FROM `snippet` WHERE `note`='NOTE ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID') LIMIT 1) AS `description`,'NO IMAGE' AS `img`,`date`,`id` FROM `notes` WHERE `user`='USER ID' 
--HAVE A PROBLEM
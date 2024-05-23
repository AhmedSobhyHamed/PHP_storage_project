

-- manga
-- change URL
UPDATE `manga` SET `url`='-------' WHERE `id`='MANGA ID' AND `user`='USER ID';
-- change image
UPDATE `manga` SET `img`='-------' WHERE `id`='MANGA ID' AND `user`='USER ID';
-- change description
UPDATE `manga` SET `description`='-------' WHERE `id`='MANGA ID' AND `user`='USER ID';
-- change last read chapter and update date
UPDATE `manga` SET `chapter`='-------' , `date`=DEFAULT WHERE `id`='MANGA ID' AND `user`='USER ID';
-- get details about manga
-- view manga details
SELECT `name`,`url`,`img`,`description`,`chapter`,`date` FROM `manga` WHERE `id`='MANGA ID' AND `user`='USER ID';

-- media
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
-- get details about media
-- view media
SELECT `name`,`weburl`,`localurl`,`tags`,`img`,`date` FROM `media` WHERE `id`='MEDIA ID' AND `user`='USER ID';

-- notes
-- add a text to note 
INSERT INTO `snippet`(`text`,`note`) VALUES('-----','NOTE ID');
IF('NOTE ID' IN (SELECT `id` FROM `notes` WHERE `user`='USER ID'),INSERT INTO `snippet`(`text`,`note`) VALUES('-----','NOTE ID')); 
INSERT INTO `snippet`(`text`,`note`) SELECT '-----' AS `text`,'NOTE ID' AS `note` WHERE 'NOTE ID' IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- delete text from the note
DELETE FROM `snippet` WHERE `id`='SNIPPET ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- edit text from note
UPDATE `snippet` SET `text`='--------' WHERE `id`='SNIPPET ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- view all text in the note
SELECT `text`,`id` FROM `snippet` WHERE `note`='NOTE ID' AND `note` IN (SELECT `id` FROM `notes` WHERE `user`='USER ID');
-- get details about note
SELECT `title` AS 'name',`date` FROM `notes` WHERE `id`='NOTE ID' AND `user`='USER ID';



-- user
-- create new user
INSERT INTO `users`(`name`,`email`,`password`,`photo`) VALUES('----','-----','-----',DEFAULT);


-- create new note with title
INSERT INTO `notes`(`title`,`user`)VALUES('-------','USER ID');
-- create a new manga with title and all other things
INSERT INTO `manga`(`name`,`url`,`img`,`description`,`chapter`,`user`)VALUES('--------',DEFAULT,DEFAULT,DEFAULT,DEFAULT,'USER ID');
-- create media with name , URL, and other things and add date 
INSERT INTO `media`(`name`,`weburl`,`localurl`,`tags`,`img`,`user`)VALUES('--------','-------',DEFAULT,DEFAULT,DEFAULT,'USER ID');


-- delete the note
-- DELETE FROM `snippet` WHERE `note`='NOTE ID' AND IF('NOTE ID' IN (SELECT `id` FROM `notes` WHERE `user`='USER ID'));
-- check clause ON DELETE CASCADE if it not work the delete rows from snippet table first
DELETE FROM `notes` WHERE `id`='NOTE ID' AND `user`='USER ID';
-- delete manga
DELETE FROM `manga` WHERE `id`='MANGA ID' AND `user`='USER ID';
-- delete media
DELETE FROM `media` WHERE `id`='MEDIA ID' AND `user`='USER ID';


-- view all notes sorted by date limited from n to n asc
SELECT `title`,`id`,`date` FROM `notes` WHERE `user`='USER ID' ORDER BY `date` ASC LIMIT N,N;
-- view all notes sorted by date limited from n to n desc
SELECT `title`,`id`,`date` FROM `notes` WHERE `user`='USER ID' ORDER BY `date` DESC LIMIT N,N;
-- view all notes sorted by name limited from n to n
SELECT `title`,`id`,`date` FROM `notes` WHERE `user`='USER ID' ORDER BY `title` ASC LIMIT N,N;

-- view all manga sorted by date  limited from n to n asc
SELECT `name`,`img`,`description`,`chapter`,`id`,`date` FROM `manga` WHERE `user`='USER ID' ORDER BY `date` ASC LIMIT N,N;
-- view all manga sorted by date  limited from n to n desc
SELECT `name`,`img`,`description`,`chapter`,`id`,`date` FROM `manga` WHERE `user`='USER ID' ORDER BY `date` DESC LIMIT N,N;
-- view all manga sorted by name  limited from n to n
SELECT `name`,`img`,`description`,`chapter`,`id`,`date` FROM `manga` WHERE `user`='USER ID' ORDER BY `name` ASC LIMIT N,N;

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
DROP TABLE IF EXISTS `incident`;
CREATE table IF NOT EXISTS `incident` (
`id` INT(5) PRIMARY KEY NOT NULL auto_increment ,
`resume` VARCHAR(250),
`description` TEXT,
`severite` INT(2),
`urgence` INT(2),
 UNIQUE KEY `id` (`id`)
);
DROP TABLE IF EXISTS `statut`;
CREATE table IF NOT EXISTS `statut` (
`id` INT(7) PRIMARY KEY NOT NULL auto_increment,
`id_incident` INT(5),
`statut` INT(3),
`date` DATETIME,
 UNIQUE KEY `id` (`id`)
);
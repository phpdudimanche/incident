# tables de la base gestionnaire incident sans ? autoincrement pour portabilite
CREATE table `c9`.`incident` (
`id` INT(5) PRIMARY KEY NOT NULL auto_increment ,
`resume` VARCHAR(250),
`description` TEXT,
`severite` INT(2),
`urgence` INT(2),
 UNIQUE KEY `id` (`id`)
)
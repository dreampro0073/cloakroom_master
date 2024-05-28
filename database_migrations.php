<?php 

// Devendra 02Nov2023

CREATE TABLE `cloakroom_master`.`clients` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NULL DEFAULT NULL , `email` VARCHAR(255) NULL DEFAULT NULL , `mobile` VARCHAR(20) NULL DEFAULT NULL , `status` TINYINT(1) NOT NULL DEFAULT '0' , `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP , `created_at` TIMESTAMP NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `users` CHANGE `session_id` `client_id` INT(11) NULL DEFAULT '0';
ALTER TABLE `cloakroom_entries` ADD `client_id` INT NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `cloakroom_penalities` ADD `client_id` INT NOT NULL DEFAULT '0' AFTER `id`;

?>
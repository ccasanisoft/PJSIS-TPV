ALTER TABLE `tec_settings` ADD `mail_incidents` VARCHAR(100) NOT NULL AFTER `advanced_sale`;

UPDATE `tec_settings` SET `mail_incidents` = 'informes@actecperu.com' WHERE `tec_settings`.`setting_id` = 1;

ALTER TABLE `tec_customers` ADD `estado` INT NOT NULL AFTER `custom_field_3`;

update `tec_customers`set `estado`=1;

UPDATE `tec_settings` SET `version` = '2.3.1' WHERE `tec_settings`.`setting_id` = 1;
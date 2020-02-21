DROP INDEX code ON tec_products;

UPDATE `tec_settings` SET `version` = '2.3.2' WHERE `tec_settings`.`setting_id` = 1;
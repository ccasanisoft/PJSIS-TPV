
ALTER TABLE `tec_settings` ADD `beta` INT NULL AFTER `nventa_number`, ADD `habilita_btn_pago` INT NULL AFTER `beta`, ADD `habilita_btn_caja` INT NULL AFTER `habilita_btn_pago`, ADD `logo_auth` VARCHAR(50) NULL AFTER `habilita_btn_caja`, ADD `pos_logo` INT NULL AFTER `logo_auth`, ADD `logo_pdf` VARCHAR(50) NULL AFTER `pos_logo`, ADD `type_imagen_pdf` INT NULL AFTER `logo_pdf`;


UPDATE `tec_settings` SET `version` = '2.2.9' WHERE `tec_settings`.`setting_id` = 1;

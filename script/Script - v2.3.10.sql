




ALTER TABLE `tec_settings` ADD `type_Print` INT NOT NULL AFTER `pdf_format`;


UPDATE tec_settings set version='2.3.9';

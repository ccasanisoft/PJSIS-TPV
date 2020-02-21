
ALTER TABLE `tec_sales` ADD `exoneratedReal` DECIMAL(25,2) NOT NULL AFTER `exonerated`;
ALTER TABLE `tec_sale_items` ADD `exonerated` INT NOT NULL AFTER `cost`;



UPDATE `tec_settings` SET version='2.2.7';
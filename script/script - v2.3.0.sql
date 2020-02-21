

ALTER TABLE `tec_sale_items` CHANGE `net_unit_price` `affect_price` DECIMAL(25,2) NOT NULL;

ALTER TABLE `tec_sales` CHANGE `exonerated` `non_affected` DECIMAL(25,2) NOT NULL, CHANGE `exoneratedReal` `exonerated` DECIMAL(25,2) NOT NULL;

ALTER TABLE `tec_sale_items` ADD `non_affected_price` DECIMAL(25,2) NOT NULL AFTER `affect_price`, ADD `exonerated_price` DECIMAL(25,2) NOT NULL AFTER `non_affected_price`;


update `tec_sale_items` set `non_affected_price`=`affect_price` where tax = 0 and `exonerated`=0;
update `tec_sale_items` set `exonerated_price`=`affect_price` where `exonerated`=1;
update `tec_sale_items` set `affect_price`=0 where tax = 0 ;
update `tec_sale_items` set `affect_price`=0 where `exonerated`=1 ;


ALTER TABLE `tec_sale_items` CHANGE `exonerated` `tax_method` INT(11) NOT NULL;


update `tec_sale_items` set `tax_method`=0 WHERE `affect_price`>0;
update `tec_sale_items` set `tax_method`=2 WHERE `non_affected_price`>0;
update `tec_sale_items` set `tax_method`=3 WHERE `exonerated_price`>0;


ALTER TABLE `tec_settings` ADD `advanced_sale` INT NOT NULL AFTER `type_imagen_pdf`;

UPDATE `tec_settings` SET `version` = '2.3.0' WHERE `tec_settings`.`setting_id` = 1;


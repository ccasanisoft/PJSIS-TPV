
CREATE TABLE `tec_locals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `cod_sunat` char(4) NOT NULL,
  `default_warehouse` int(11) DEFAULT NULL,
  `invoice_format` varchar(50) DEFAULT NULL,
  `bill_format`	varchar(50) DEFAULT NULL,
  `invoice_number` int(11) DEFAULT NULL,
  `bill_number`	int(11) DEFAULT NULL,
  `uCrea` int(11) DEFAULT NULL,
  `fCrea` datetime DEFAULT NULL,
  `uActualiza` int(11) DEFAULT NULL,
  `fActualiza` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_sales`
ADD `local_id` int(11) NOT NULL AFTER `date`;

ALTER TABLE `tec_purchases`
ADD `local_id` int(11) NOT NULL AFTER `date`;

ALTER TABLE `tec_product_mov`
ADD `local_id` int(11) NOT NULL AFTER `origin_id`;

UPDATE `tec_sales`
SET `local_id` = 1;

UPDATE `tec_purchases`
SET `local_id` = 1;

UPDATE `tec_product_mov`
SET `local_id` = 1;

ALTER TABLE `tec_sales`
ADD `warehouse_id` int(11) NOT NULL AFTER `local_id`;

UPDATE `tec_sales`
SET `warehouse_id` = 1;


ALTER TABLE `tec_settings`
ADD `nventa_format` VARCHAR(50) NOT NULL,
ADD `nventa_number` int(11) NOT NULL;

UPDATE `tec_settings`
SET `nventa_format`='NV001-{0000}', nventa_number=0;


INSERT INTO `tec_mov_motive`(`id`, `motive`, `alias`) VALUES (8,"Salida por anulación de compra","SAC");

INSERT INTO `tec_locals` (`id`, `code`, `name`, `address`, `cod_sunat`, `default_warehouse`, `invoice_format`, `bill_format`, `invoice_number`, `bill_number`, `uCrea`, `fCrea`, `uActualiza`, `fActualiza`, `estado`) VALUES
(1, 'Principal', 'Local Principal', '', '0001', NULL, NULL, NULL, NULL, NULL, 1, NOW(), NULL, NULL, 1);

UPDATE `tec_settings` SET version='2.2.0';

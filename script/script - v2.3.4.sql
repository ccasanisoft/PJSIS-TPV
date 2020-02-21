
ALTER TABLE `tec_sale_items` ADD `tax_ICBPER` DECIMAL(25,2) NOT NULL AFTER `igv`;

ALTER TABLE `tec_sales` ADD `tax_ICBPER` DECIMAL(25,2) NOT NULL AFTER `total_tax`;

ALTER TABLE `tec_sales` ADD `mult_ICBPER` DECIMAL(25,2) NOT NULL AFTER `tax_ICBPER`;

CREATE TABLE `tec_tax_icbper` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `amount` decimal(25,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tec_tax_icbper` (`id`, `year`, `amount`) VALUES
(1, 2019, '0.10'),
(2, 2020, '0.20'),
(3, 2021, '0.30'),
(4, 2022, '0.40'),
(5, 2023, '0.50');

ALTER TABLE `tec_tax_icbper` ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_tax_icbper`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `tec_settings` ADD `tax_icbper` INT NOT NULL AFTER `advanced_sale`;

ALTER TABLE `tec_sale_items` ADD `quantity_ICBPER` INT NOT NULL AFTER `tax_ICBPER`;

UPDATE `tec_settings` SET `version` = '2.3.4' WHERE `tec_settings`.`setting_id` = 1;
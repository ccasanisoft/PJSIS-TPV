
/* *****************************************************************************
*
* Last edited: 31 de Julio 2018
* Update: Diego Gomez
*
**************************************************************************** */

-- 4 y 5 ---------------------------------------------------- 20180719
ALTER TABLE `tec_categories`
ADD `parent_category_id` int(11) NOT NULL AFTER `code`;

-- 10 ------------------------------------------------------- 20180721
ALTER TABLE `tec_customers`
ADD `direccion` VARCHAR(300) NULL;

ALTER TABLE `tec_sales`
ADD `document_type` INT NULL AFTER `customer_name`;

-- 1 -------------------------------------------------------- 20180725
-- Campos en la tabla clientes
ALTER TABLE `tec_customers`
ADD `customers_type_id` INT NULL ,
ADD `digemid` VARCHAR(250) NULL ;

-- Nueva tabla tipo de clientes
CREATE TABLE IF NOT EXISTS `tec_customers_type`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customers_type` VARCHAR(250) DEFAULT NULL,
    PRIMARY KEY (`id`)
)

-- Punto 2 -------------------------------------------------- 20180719
ALTER TABLE `tec_suppliers`
ADD `digemid` VARCHAR(250) NULL AFTER `email`;

-- Punto 3 -------------------------------------------------- 20180724

-- Campos laboratorio, principio activo
--y accion farmacologica a tabla Productos
ALTER TABLE `tec_products`
ADD `laboratory_id` INT NULL AFTER `alert_quantity`,
ADD `active_principle` VARCHAR(250) NULL AFTER `laboratory_id`,
ADD `pharmacological_action` VARCHAR(250) NULL AFTER `active_principle`;

DROP TABLE IF EXISTS `tec_laboratories`;
CREATE TABLE IF NOT EXISTS `tec_laboratories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `laboratory` VARCHAR(250) NOT NULL,
  PRIMARY KEY `id` (`id`)
)

-- Volcado de datos para la tabla `tec_laboratories`
-- INSERT INTO `tec_laboratories` (`id`, `laboratory`)
-- VALUES (1, 'laboratorio 1'), (2, 'laboratorio 2');

-- Punto 7 -------------------------------------------------- 20180724
ALTER TABLE `tec_sales`
ADD `canal_id` INT NULL AFTER `rounding`;

-- Agregar CMP y Medico en Venta ---------------------------- 20180730
ALTER TABLE `tec_sales`
ADD `cmp` VARCHAR(6) NULL,
ADD `doctor` VARCHAR(250) NULL;

-- Agregar campo Presentacion a Productos -------------------- 20180731
ALTER TABLE `tec_products`
ADD `presentation` VARCHAR(250) NULL;

-- Agregar campo canal_id a Suspended_sales ------------------ 20180813
ALTER TABLE `tec_suspended_sales`
ADD `canal_id` INT NULL;

-- 4 --------------------------------------------------------- 20180822
CREATE TABLE `tec_warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- 5 --------------------------------------------------------- 20180822
ALTER TABLE `tec_purchases` ADD `warehouse_id` INT NOT NULL AFTER `received`;

/*STOCK POR ALMACEN*/
CREATE TABLE `tec_warehouse_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Facturacion electronica ----------------------------------- 20180829
ALTER TABLE `tec_sales`
ADD `invoice_id` VARCHAR(12) NULL,
ADD `flg_response` INT(1) NULL,
ADD `error_code` VARCHAR(15) NULL,
ADD `response_descrip` VARCHAR(250) NULL,
ADD `digest_value` VARCHAR(250) NULL;

-- Almacen por defecto --------------------------------------- 20180831
ALTER TABLE `tec_settings`
ADD `default_warehouse` int(11) NOT NULL;

-- CMP ------------------------------------------------------- 20180902
CREATE TABLE `tec_cmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `cmp` varchar(50) NOT NULL,
  `speciality` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Perfil Caja ----------------------------------------------- 20180902
INSERT INTO `tec_groups` (`id`, `name`, `description`)
VALUES ( 3, 'caja', 'Caja');

-- Codigo de Factura y Boleta(Actual) ------------------------ 20180903
ALTER TABLE `tec_settings`
ADD `invoice_format` VARCHAR(50) NOT NULL,
ADD `bill_format` VARCHAR(50) NOT NULL,
ADD `invoice_number` int(11) NOT NULL,
ADD `bill_number` int(11) NOT NULL;

UPDATE `tec_settings`
SET `invoice_format`='F001-{0000}',`bill_format`= 'B001-{0000}', invoice_number=0, bill_number=0
WHERE 1;

-- PDF BOLETA Y FACTURA -------------------------------------- 20180907
CREATE TABLE `tec_sales_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `file_name` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Movimientos ----------------------------------------------- 20180920
CREATE TABLE `tec_mov_motive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motive` varchar(250) NOT NULL,
  `alias` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- data for table `tec_mov_motive`
INSERT INTO `tec_mov_motive` (`id`, `motive`, `alias`) VALUES
(1, ' Ingreso por compra de productos', 'ICP'),
(2, 'Ingreso por devolución de productos', 'IDP'),
(3, 'Ingreso por anulación de venta', 'IAV'),
(4, 'Ingreso por traslado', 'IT'),
(5, 'Salida por venta', 'SV'),
(6, 'Salida por traslado', 'ST');

CREATE TABLE `tec_product_mov` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `cant` int(11) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `idMotivo` int(11) DEFAULT NULL,
  `ref` char(50) DEFAULT NULL,
  `uCrea` int(11) DEFAULT NULL,
  `fCrea` date DEFAULT NULL,
  `uActualiza` int(11) DEFAULT NULL,
  `fActualiza` date DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Estado Anulado -------------------------------------------- 20180920
ALTER TABLE `tec_sales`
ADD `estado` INT(1);

-- Tabla Movimientos fecha y hora ---------------------------- 20180925
ALTER TABLE `tec_product_mov`
CHANGE `fCrea` `fCrea` TIMESTAMP NULL DEFAULT NULL,
CHANGE `fActualiza` `fActualiza` TIMESTAMP NULL DEFAULT NULL;

-- Traslados ------------------------------------------------- 20180928
CREATE TABLE `tec_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num` varchar(100) DEFAULT NULL,
  `ref` varchar(250) DEFAULT NULL,
  `warehouse_origin_id` int(11) DEFAULT NULL,
  `warehouse_destiny_id` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `uCrea` int(11) DEFAULT NULL,
  `fCrea` timestamp NULL DEFAULT NULL,
  `uActualiza` int(11) DEFAULT NULL,
  `fActualiza` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `tec_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `stock_prev` int(11) DEFAULT NULL,
  `stock_new` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Identificador del origen de un movimiento ----------------- 20181004
ALTER TABLE `tec_product_mov`
ADD `origin_id` int(11) NOT NULL AFTER `id`;

ALTER TABLE `tec_purchases`
ADD `estado` int(11) DEFAULT 1,
ADD `uCrea` int(11) DEFAULT NULL,
ADD `fCrea` timestamp NULL DEFAULT NULL,
ADD `uActualiza` int(11) DEFAULT NULL,
ADD `fActualiza` timestamp NULL DEFAULT NULL;

ALTER TABLE `tec_purchase_items`
ADD `estado` int(11) DEFAULT 1,
ADD `uCrea` int(11) DEFAULT NULL,
ADD `fCrea` timestamp NULL DEFAULT NULL,
ADD `uActualiza` int(11) DEFAULT NULL,
ADD `fActualiza` timestamp NULL DEFAULT NULL;

-- Response Descrip mas caracteres --------------------------- 20181010
ALTER TABLE `tec_sales` CHANGE `response_descrip` `response_descrip`
VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- Auditoria para productos ---------------------------------- 20181019
ALTER TABLE `tec_products`
ADD `estado` int(11) DEFAULT 1,
ADD `uCrea` int(11) DEFAULT NULL,
ADD `fCrea` timestamp NULL DEFAULT NULL,
ADD `uActualiza` int(11) DEFAULT NULL,
ADD `fActualiza` timestamp NULL DEFAULT NULL

UPDATE `tec_products` SET `alert_quantity`= 0

-- Resumen Boletas ------------------------------------------- 20180929
CREATE TABLE `tec_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_date` date DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `file_name` char(50) DEFAULT NULL,
  `nventa` varchar(1000) DEFAULT NULL, --//*****Renato TRJ023 25/04/2019   ********--
  `reference_date` date DEFAULT NULL,
  `processed_date` date DEFAULT NULL,
  `type` char(50) DEFAULT NULL,
  `flg_response` INT(1) NULL,
  `error_code` VARCHAR(15) NULL,
  -- `response_descrip` varchar(1000) DEFAULT NULL,
  `observations` TEXT NULL,
  `summary_status` INT(1) NULL,
  `uCrea` int(11) DEFAULT NULL,
  `fCrea` datetime DEFAULT NULL,
  `uActualiza` int(11) DEFAULT NULL,
  `fActualiza` datetime DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Envio de comprobantes ------------------------------------- 20181108
ALTER TABLE `tec_summary`
ADD `response_descrip` varchar(1000) DEFAULT NULL AFTER `error_code`;

ALTER TABLE `tec_summary` CHANGE COLUMN `summary_status` `status` INT(1) NULL;

ALTER TABLE `tec_summary` RENAME `tec_send_invoice`;

CREATE TABLE `tec_send_invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_invoice_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `uCrea` int(11) DEFAULT NULL,
  `fCrea` datetime DEFAULT NULL,
  `uActualiza` int(11) DEFAULT NULL,
  `fActualiza` datetime DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Cambio en las tablas ------------------------------------- 20181113

ALTER TABLE `tec_customers` CHANGE COLUMN `digemid` `custom_field_1` varchar(250) NULL;
ALTER TABLE `tec_suppliers` CHANGE COLUMN `digemid` `custom_field_1` varchar(250) NULL;
ALTER TABLE `tec_sales` CHANGE COLUMN `cmp` `custom_field_1` varchar(250) NULL;
ALTER TABLE `tec_sales` CHANGE COLUMN `doctor` `custom_field_2` varchar(250) NULL;
ALTER TABLE `tec_products` CHANGE COLUMN `active_principle` `custom_field_1` varchar(250) NULL;
ALTER TABLE `tec_products` CHANGE COLUMN `pharmacological_action` `custom_field_2` varchar(250) NULL;
ALTER TABLE `tec_products` CHANGE COLUMN `laboratory_id` `maker_id` int(11) NULL;

ALTER TABLE `tec_customers`
ADD `custom_field_2` varchar(250) DEFAULT NULL AFTER `custom_field_1`,
ADD `custom_field_3` varchar(250) DEFAULT NULL AFTER `custom_field_2`;

ALTER TABLE `tec_suppliers`
ADD `custom_field_2` varchar(250) DEFAULT NULL AFTER `custom_field_1`,
ADD `custom_field_3` varchar(250) DEFAULT NULL AFTER `custom_field_2`;

ALTER TABLE `tec_sales`
ADD `custom_field_3` varchar(250) DEFAULT NULL AFTER `custom_field_2`;

ALTER TABLE `tec_products`
ADD `custom_field_3` varchar(250) DEFAULT NULL AFTER `custom_field_2`;

ALTER TABLE `tec_cmp` RENAME `tec_doctors`;
ALTER TABLE `tec_laboratories` RENAME `tec_makers`;

ALTER TABLE `tec_makers` CHANGE COLUMN `laboratory` `maker` varchar(250) NULL;

-- Grand_total = Gravadas + IGV + Redondeo ----------------- 20181114

ALTER TABLE `tec_sales`
ADD `subtotal` decimal(25,2) NOT NULL AFTER `total_tax`;

-------------------v2.1.0-------------------------------------------------
--------------- Compras Pago ------------------------------- 20181220

CREATE TABLE `tec_purchase_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `amount` decimal(25,2) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_purchases` CHANGE COLUMN `total` `grand_total` decimal(25,2) NOT NULL;

ALTER TABLE `tec_purchases`
ADD `total` decimal(25,2) NOT NULL AFTER `note`,
ADD `paid` decimal(25,2) NOT NULL AFTER `grand_total`,
ADD `status` varchar(250) DEFAULT NULL AFTER `paid`,
ADD `description` varchar(250) DEFAULT NULL AFTER `status`,
ADD `affected` decimal(25,2) NOT NULL AFTER `note`,
ADD `exonerated` decimal(25,2) NOT NULL AFTER `affected`,
ADD `tax` decimal(25,2) NOT NULL AFTER `exonerated`,
ADD `expiration_date` date NULL AFTER `received`;
/* ADD `document_type` INT NULL AFTER `id`; */

UPDATE
    `tec_purchases`
SET
    `paid` = `grand_total`,
    `status` = "Pagado",
    `total` = ROUND(`grand_total` / 1.18, 2),
    `affected` = `total`,
    `tax` = `grand_total` - `total`;

-- Exonerado ----------------------------------------------- 20181114

ALTER TABLE `tec_sales`
ADD `affected` decimal(25,2) NOT NULL AFTER `document_type`,
ADD `exonerated` decimal(25,2) NOT NULL AFTER `affected`;

ALTER TABLE `tec_sale_items`
ADD `prr_discount` decimal(25,2) NOT NULL AFTER `item_discount`,
ADD `igv` varchar(20) NOT NULL AFTER `item_tax`;

ALTER TABLE `tec_sale_items` CHANGE `tax` `tax` DECIMAL(25,2) NOT NULL;

UPDATE `tec_sales` SET `affected`= `total`;

-- Generar Venta --------------------------------------------- 20181226

ALTER TABLE `tec_sales`
ADD `attachment` varchar(255) DEFAULT NULL AFTER `rounding`,
ADD `expiration_date` date DEFAULT NULL AFTER `attachment`;

UPDATE `tec_sale_items` SET `igv`= `tax`;
UPDATE `tec_sale_items` SET `tax`= `item_tax` / `quantity`;

ALTER TABLE tec_sales
DROP COLUMN `summary_creation_id`,
DROP COLUMN `summary_annulation_id`;

-- Moneda ---------------------------------------------------- 20190102

ALTER TABLE `tec_sales`
ADD `currency` varchar(3) DEFAULT NULL AFTER `document_type`;

ALTER TABLE `tec_purchases`
ADD `currency` varchar(3) DEFAULT NULL AFTER `note`;

ALTER TABLE `tec_expenses`
ADD `currency` varchar(3) DEFAULT NULL AFTER `amount`;

ALTER TABLE `tec_products`
ADD `currency` varchar(3) DEFAULT NULL AFTER `category_id`;

-- Redondeo -------------------------------------------------- 20190104
-- subtotal(Sumatoria de productos += Cant * Precio) y amount(Gravado + IGV)

ALTER TABLE `tec_sales`
ADD `amount` decimal(25,2) NOT NULL AFTER `subtotal`;

UPDATE `tec_sales` SET `amount`= `grand_total` - `rounding`;

-- Tipo de Cambio --------------------------------------------- 20190102

UPDATE `tec_products` SET `currency`= "PEN";
UPDATE `tec_purchases` SET `currency`= "PEN";
UPDATE `tec_expenses` SET `currency`= "PEN";
UPDATE `tec_sales` SET `currency`= "PEN";
UPDATE `tec_payments` SET `currency`= "PEN";

CREATE TABLE `tec_exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  -- `coin_from` varchar(3) DEFAULT NULL,
  -- `coin_to` varchar(3) DEFAULT NULL,
  -- `exchange` decimal(25,3) NOT NULL,
  `buy` decimal(25,3) NOT NULL,
  `sell` decimal(25,3) NOT NULL,
  `uCrea` int(11) DEFAULT NULL,
  `fCrea` datetime DEFAULT NULL,
  `uActualiza` int(11) DEFAULT NULL,
  `fActualiza` datetime DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Agregar en el detalle Nombre y Codigo de Producto ---------- 20190108

ALTER TABLE `tec_sale_items`
ADD `code` varchar(50) DEFAULT NULL AFTER `product_id`,
ADD `name` char(255) NOT NULL AFTER `code`;

UPDATE `tec_sale_items` i
INNER JOIN tec_products p ON i.product_id = p.id
SET i.code = p.code, i.name = p.name;

ALTER TABLE `tec_sale_items` CHANGE `product_id` `product_id` INT(11) NULL;

--------------------------------------------------------------------------
ALTER TABLE `tec_sales`
ADD `exchange` decimal(25,3) DEFAULT NULL AFTER `currency`;

ALTER TABLE `tec_purchases`
ADD `exchange` decimal(25,3) DEFAULT NULL AFTER `currency`;

ALTER TABLE `tec_expenses`
ADD `exchange` decimal(25,3) DEFAULT NULL AFTER `currency`;

-----------------------------------------------------------------------------

ALTER TABLE `tec_sale_items`
ADD `currency_cost` varchar(3) DEFAULT NULL AFTER `real_unit_price`;

UPDATE `tec_sale_items` SET `currency_cost`= "PEN";

UPDATE `tec_settings` SET `version`= "2.1.0";

------------------------------v2.2.0---------------------------------------------

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

-----------------------------------NVENTA---------------------
ALTER TABLE `tec_settings`
ADD `nventa_format` VARCHAR(50) NOT NULL,  --//*****Renato TRJ023 25/04/2019   ********--
ADD `nventa_number` int(11) NOT NULL;  --//*****Renato TRJ023 25/04/2019   ********--

UPDATE `tec_settings`
SET `nventa_format`='NV001-{0000}', nventa_number=0; --//*****Renato TRJ023 25/04/2019   ********--

------------------------------------------------------------

/* INSERT INTO `tec_mov_motive`(`id`, `motive`, `alias`) VALUES (7,"Ingreso por inventario","II") */
INSERT INTO `tec_mov_motive`(`id`, `motive`, `alias`) VALUES (8,"Salida por anulación de compra","SAC");

INSERT INTO `tec_locals` (`id`, `code`, `name`, `address`, `cod_sunat`, `default_warehouse`, `invoice_format`, `bill_format`, `invoice_number`, `bill_number`, `uCrea`, `fCrea`, `uActualiza`, `fActualiza`, `estado`) VALUES
(1, 'Principal', 'Local Principal', '', '0001', NULL, NULL, NULL, NULL, NULL, 1, NOW(), NULL, NULL, 1);

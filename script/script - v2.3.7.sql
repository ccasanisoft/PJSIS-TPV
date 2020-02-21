
-- procedure REGISTRO DE VENTA

DELIMITER $$
CREATE PROCEDURE `register_sale`(IN `dateP` VARCHAR(20), IN `local_idP` INT, IN `warehouse_idP` INT, IN `customer_idP` INT, IN `customer_nameP` VARCHAR(55), IN `document_typeP` INT, IN `currencyP` VARCHAR(3), IN `exchangeP` VARCHAR(50), IN `affectedP` VARCHAR(50), IN `non_affectedP` VARCHAR(50), IN `exoneratedP` VARCHAR(50), IN `totalP` VARCHAR(50), IN `product_discountP` VARCHAR(50), IN `order_discount_idP` VARCHAR(20), IN `order_discountP` VARCHAR(50), IN `total_discountP` VARCHAR(50), IN `product_taxP` VARCHAR(50), IN `order_tax_idP` VARCHAR(20), IN `order_taxP` VARCHAR(50), IN `total_taxP` VARCHAR(50), IN `tax_ICBPERP` VARCHAR(50), IN `mult_ICBPERP` VARCHAR(50), IN `subtotalP` VARCHAR(50), IN `amountP` VARCHAR(50), IN `grand_totalP` VARCHAR(50), IN `total_itemsP` INT, IN `total_quantityP` VARCHAR(50), IN `paidP` VARCHAR(50), IN `created_byP` INT, IN `updated_byP` INT, IN `updated_atP` VARCHAR(20), IN `noteP` VARCHAR(1000), IN `statusP` VARCHAR(20), IN `roundingP` VARCHAR(50), IN `attachmentP` VARCHAR(255), IN `expiration_dateP` VARCHAR(20), IN `canal_idP` INT, IN `custom_field_1P` VARCHAR(250), IN `custom_field_2P` VARCHAR(250), IN `custom_field_3P` VARCHAR(250), IN `invoice_idP` VARCHAR(20), IN `flg_responseP` INT, IN `error_codeP` VARCHAR(15), IN `response_descripP` VARCHAR(2000), IN `digest_valueP` VARCHAR(250), IN `estadoP` INT, OUT `result_id` INT, OUT `result_invoice` VARCHAR(100), IN `itemsSale` LONGTEXT, IN `QuantityItemsSale` INT)
    NO SQL
BEGIN
	DECLARE correlativo VARCHAR(100);
    DECLARE countmin INT;
    SET countmin = 1;
    
    IF(document_typeP = 2) THEN
    
        SET correlativo := (SELECT CONCAT("F001-", REPEAT( '0', 7 - LENGTH( invoice_number + 1) ) , invoice_number + 1)  FROM tec_settings);

    ELSE
    
        SET correlativo := (SELECT CONCAT("B001-", REPEAT( '0', 7 - LENGTH( bill_number + 1) ) , bill_number + 1)  FROM tec_settings);

    END IF;



	INSERT INTO `tec_sales`(`date`, `local_id`, `warehouse_id`, `customer_id`, 
                        `customer_name`, `document_type`, `currency`, `exchange`, 
                        `affected`, `non_affected`, `exonerated`, `total`, 
                        `product_discount`, `order_discount_id`, `order_discount`, `total_discount`, 
                        `product_tax`, `order_tax_id`, `order_tax`, `total_tax`, 
                        `tax_ICBPER`, `mult_ICBPER`, `subtotal`, `amount`, 
                        `grand_total`, `total_items`, `total_quantity`, `paid`, 
                        `created_by`, `updated_by`, `updated_at`, `note`, 
                        `status`, `rounding`, `attachment`, `expiration_date`, 
                        `canal_id`, `custom_field_1`, `custom_field_2`, `custom_field_3`, 
                        `invoice_id`, `flg_response`, `error_code`, `response_descrip`, 
                        `digest_value`, `estado`) 
                        VALUES ( dateP, local_idP, warehouse_idP, customer_idP, 
                                customer_nameP, document_typeP, currencyP, exchangeP, 
                                affectedP, non_affectedP, exoneratedP, totalP, 
                                product_discountP, order_discount_idP, order_discountP, total_discountP, 
                                product_taxP, order_tax_idP, order_taxP, total_taxP, 
                                tax_ICBPERP, mult_ICBPERP, subtotalP, amountP, 
                                grand_totalP, total_itemsP, total_quantityP, paidP, 
                                created_byP, updated_byP, updated_atP, noteP, 
                                statusP, roundingP, attachmentP, expiration_dateP, 
                                canal_idP, custom_field_1P, custom_field_2P, custom_field_3P, 
                                correlativo, flg_responseP, error_codeP, response_descripP, 
                                digest_valueP, estadoP);

    IF(document_typeP = 2) THEN

    	UPDATE tec_settings SET invoice_number = invoice_number + 1;

    ELSE
    
    	UPDATE tec_settings SET bill_number = bill_number + 1;

    END IF;
    
    
    SET result_id :=  (SELECT LAST_INSERT_ID());
    SET result_invoice := (correlativo);
    -- se comento este while ya que no se tenia version MySQL 5.7 o MariaDB 10.4
    /*
    WHILE countmin < QuantityItemsSale DO
    
   
     INSERT INTO `tec_sale_items`(`sale_id`, `product_id`, `code`, 
                                  `name`, `quantity`, `unit_price`, 
                                  `affect_price`, `non_affected_price`, `exonerated_price`, 
                                  `discount`, `item_discount`, `prr_discount`, 
                                  `tax`, `item_tax`, `igv`, `tax_ICBPER`, 
                                  `quantity_ICBPER`, `subtotal`, `real_unit_price`, 
                                  `currency_cost`, `cost`, `tax_method`) 
                                  VALUES (
          result_id,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.product_id') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.code') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.name') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Quantity') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Unit_price') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Affect_price') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Non_affected_price') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Exonerated_price') AS 'Result') ,'"','') ,
		  REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.discount') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.item_discount') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.prr_discount') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Tax') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Item_tax') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Igv') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Tax_ICBPER') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Quantity_ICBPER') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.Subtotal') AS 'Result') ,'"',''),
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.real_unit_price') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.currency_cost') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.cost') AS 'Result') ,'"','') ,
          REPLACE((SELECT JSON_EXTRACT(JSON_EXTRACT(JSON_EXTRACT(itemsSale, '$.items'), CONCAT('$.',countmin)), '$.tax_method') AS 'Result') ,'"','')
         );
         
         SET countmin = countmin + 1;
        
    END WHILE;*/
END$$
DELIMITER ;

-- procedure TIPO DE CAMBIO

DELIMITER $$
CREATE PROCEDURE `type_Exchange`()
    NO SQL
BEGIN

SELECT sell FROM `tec_exchange` ORDER BY `id` DESC limit 1;

END$$
DELIMITER ;

-- procedure VALIDAR USUARIO

DELIMITER $$
CREATE PROCEDURE `validate_customer`(IN `numIdentif` VARCHAR(20))
    NO SQL
BEGIN

        SELECT id, name, cf1, cf2, direccion, document_type_id FROM tec_customers WHERE (cf2 = numIdentif or cf1 = numIdentif) and estado = 1 limit 1;

END$$
DELIMITER ;

-- procedure VALIDAR BETA

DELIMITER $$
CREATE  PROCEDURE `validate_beta`()
    NO SQL
BEGIN

SELECT beta FROM `tec_settings` ORDER BY `setting_id` DESC limit 1;

END$$
DELIMITER ;

-- procedure UPDATE GENERAR XML

DELIMITER $$
CREATE PROCEDURE `update_Xml_sale`(IN `idP` INT, IN `flg_responseP` INT, IN `error_codeP` VARCHAR(15), IN `response_descripP` VARCHAR(2000), IN `digest_valueP` VARCHAR(250))
    NO SQL
BEGIN

update `tec_sales`
SET 
flg_response = flg_responseP,
error_code = error_codeP, 
response_descrip = response_descripP,
digest_value = digest_valueP
where id = idP;

END$$
DELIMITER ;

-- procedure REGISTRO DE SEND INVOICE y SEND INVOICE ITEMS

DELIMITER $$
CREATE PROCEDURE `register_Send_invoice`(IN `issue_dateP` VARCHAR(50), IN `file_nameP` VARCHAR(50), IN `reference_dateP` VARCHAR(50), IN `processed_dateP` VARCHAR(50), IN `typeP` VARCHAR(50), IN `flg_responseP` INT, IN `error_codeP` VARCHAR(15), IN `response_descripP` VARCHAR(1000), IN `statusP` INT, IN `uCreaP` INT, IN `fCreaP` VARCHAR(50), IN `estadoP` INT, IN `sale_idP` INT)
    NO SQL
BEGIN

DECLARE resultadoID INT;

INSERT INTO `tec_send_invoice`(
    `issue_date`, `number`, `file_name`, 
    `ticket`, `reference_date`, `processed_date`, 
    `type`, `flg_response`, `error_code`, 
    `response_descrip`, `observations`, `status`, 
    `uCrea`, `fCrea`, `uActualiza`, 
    `fActualiza`, `estado`) VALUES (
        issue_dateP, null, file_nameP, 
        null, reference_dateP, processed_dateP,
        typeP, flg_responseP, error_codeP,
        response_descripP, null, statusP,
        uCreaP, fCreaP, null,
        null, estadoP
    );
    
    SET resultadoID := (SELECT LAST_INSERT_ID());
    
    INSERT INTO `tec_send_invoice_items`(`send_invoice_id`, `sale_id`, `uCrea`, 
                                         `fCrea`, `uActualiza`, `fActualiza`, 
                                         `estado`) VALUES (
                                             resultadoID, sale_idP, uCreaP, 
                                             fCreaP, null, null, 
                                             estadoP);

END$$
DELIMITER ;

-- procedure REGISTRO DE CUSTOMER

DELIMITER $$
CREATE PROCEDURE `register_customer`(OUT `result_id` INT, IN `nameP` VARCHAR(55), IN `cf1P` VARCHAR(255), IN `cf2P` VARCHAR(255), IN `phoneP` VARCHAR(20), IN `emailP` VARCHAR(100), IN `direccionP` VARCHAR(300), IN `customers_type_idP` INT, IN `estadoP` INT, IN `person_typeP` VARCHAR(20), IN `document_type_idP` INT)
    NO SQL
BEGIN



INSERT INTO `tec_customers`(`name`, `cf1`, `cf2`, 
                            `phone`, `email`, `direccion`, 
                            `customers_type_id`, `estado`, `person_type`, 
                            `document_type_id`) VALUES (
                                nameP, cf1P, cf2P,
                            	phoneP, emailP, direccionP,
                            	customers_type_idP, estadoP, person_typeP,
                            	document_type_idP);

SET result_id :=  (SELECT LAST_INSERT_ID());

END$$
DELIMITER ;



-- registro de VERSIONES

UPDATE `tec_settings` SET `version` = '2.3.7' WHERE `tec_settings`.`setting_id` = 1;

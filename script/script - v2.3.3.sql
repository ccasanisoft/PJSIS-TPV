CREATE TABLE `tec_credit_note_motive` (
  `id` int(11) NOT NULL,
  `description_NC` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_credit_note_motive`  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `tec_credit_note_motive` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `tec_credit_note_motive` (`id`, `description_NC`) VALUES
(1, 'Anulación de la operación'),
(2, 'Anulación por error en el RUC'),
(3, 'Corrección por error en la descripción'),
(4, 'Descuento global'),
(5, 'Descuento por Item'),
(6, 'Devolución total'),
(7, 'Devolución parcial');

ALTER TABLE `tec_settings` ADD `note_credit_invoice_format` VARCHAR(50) NOT NULL AFTER `nventa_number`, ADD `note_credit_invoice_number` INT NOT NULL AFTER `note_credit_invoice_format`;

UPDATE `tec_settings` SET `note_credit_invoice_format` = 'FC01-{0000000}' WHERE `tec_settings`.`setting_id` = 1;

UPDATE `tec_settings` SET `note_credit_invoice_number` = '0' WHERE `tec_settings`.`setting_id` = 1;

ALTER TABLE `tec_settings` ADD `note_credit_bill_format` VARCHAR(50) NOT NULL AFTER `note_credit_invoice_number`, ADD `note_credit_bill_number` INT NOT NULL AFTER `note_credit_bill_format`;

UPDATE `tec_settings` SET `note_credit_bill_format` = 'BC01-{0000000}' WHERE `tec_settings`.`setting_id` = 1;

UPDATE `tec_settings` SET `note_credit_bill_number` = '0' WHERE `tec_settings`.`setting_id` = 1;

CREATE TABLE `tec_credit_note` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `responseCode` int(11) NOT NULL,
  `invoiceTypeCode` int(11) NOT NULL,
  `serieNumero` varchar(20) NOT NULL,
  `issueDate` date NOT NULL,
  `issueTime` time NOT NULL,
  `documentCurrencyCode` varchar(10) NOT NULL,
  `customerDocumentID` int(11) NOT NULL,
  `supplierDocumentID` int(11) NOT NULL,
  `customerID` varchar(255) NOT NULL,
  `customerName` varchar(100) NOT NULL,
  `taxAmount` decimal(25,2) NOT NULL,
  `taxableAmount` decimal(25,2) NOT NULL,
  `non_affected` decimal(25,2) NOT NULL,
  `exonerated` decimal(25,2) NOT NULL,
  `taxSubtotal` decimal(25,2) NOT NULL,
  `payableAmount` decimal(25,2) NOT NULL,
  `referenceID` varchar(20) NOT NULL,
  `DocumentTypeCode` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `user_upgrade` int(11) DEFAULT NULL,
  `date_upgrade` datetime DEFAULT NULL,
  `user_create` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `flg_response` int(11) DEFAULT NULL,
  `error_code` varchar(15) DEFAULT NULL,
  `response_descrip` varchar(2000) DEFAULT NULL,
  `digest_value` varchar(250) DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_credit_note`  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `tec_credit_note`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `tec_credit_note_items` (
  `id` int(11) NOT NULL,
  `credit_note_id` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `LineExtensionAmount` int(11) NOT NULL,
  `PricingReference` int(11) NOT NULL,
  `PriceTypeCode` int(11) NOT NULL,
  `TaxTotalTaxAmount` decimal(25,2) NOT NULL,
  `TaxSubtotalTaxableAmount` int(11) NOT NULL,
  `PriceAmount` int(11) NOT NULL,
  `TaxSubtotalTaxAmount` decimal(25,2) NOT NULL,
  `TaxPercent` int(11) NOT NULL,
  `tax_method` int(11) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `itemSellersID` varchar(50) NOT NULL,
  `user_upgrade` int(11) DEFAULT NULL,
  `date_upgrade` datetime DEFAULT NULL,
  `user_create` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_credit_note_items`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_credit_note_items`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `tec_send_invoice_nc` (
  `id` int(11) NOT NULL,
  `note_credit_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `flg_response` int(11) NOT NULL,
  `error_code` varchar(15) NOT NULL,
  `response_descrip` varchar(1000) NOT NULL,
  `observations` text,
  `status` int(11) NOT NULL,
  `user_Create` int(11) NOT NULL,
  `date_Create` datetime NOT NULL,
  `user_upgrade` int(11) DEFAULT NULL,
  `date_upgrade` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_send_invoice_nc`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_send_invoice_nc`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `tec_credit_note_files` (
  `id` int(11) NOT NULL,
  `credit_note_id` int(11) NOT NULL,
  `file_name` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_credit_note_files`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_credit_note_files`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- store procedure para registrar header de nota de credito

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `increment_bill`(IN `sale_i` INT, IN `responseCod` INT, IN `invoiceTypeCod` INT, IN `serieNumer` VARCHAR(20), IN `issueDat` DATE, IN `issueTim` TIME, IN `documentCurrencyCod` VARCHAR(10), IN `customerDocumentI` INT, IN `supplierDocumentI` INT, IN `customerI` VARCHAR(255), IN `customerNam` VARCHAR(100), IN `taxAmoun` DECIMAL, IN `taxableAmoun` DECIMAL, IN `taxSubtota` DECIMAL, IN `payableAmoun` DECIMAL, IN `referenceI` VARCHAR(20), IN `DocumentTypeCod` INT, IN `descriptio` VARCHAR(100), IN `user_creat` INT, IN `date_creat` DATETIME, IN `estad` INT, IN `result02` INT ZEROFILL, OUT `resultado` INT, OUT `resultSERIEcorre` VARCHAR(12), IN `non_affecte` DECIMAL, IN `exonerate` DECIMAL)
    NO SQL
BEGIN
    DECLARE countmin INT;
    DECLARE correlativo INT;
    DECLARE validarExistencia Varchar(20);
    DECLARE invoice Varchar(12);
    DECLARE tipoComProbante varchar(1);
    SET countmin = 0;
    
    SET tipoComProbante := SUBSTRING(referenceI, 1, 1);
    
    SET validarExistencia := (SELECT id FROM tec_credit_note WHERE sale_id = sale_i limit 1);
    
    IF(validarExistencia IS NULL) THEN
    
    	IF STRCMP(tipoComProbante, 'F') = 0 THEN
    
        	WHILE countmin < 1 DO
            	UPDATE tec_settings SET note_credit_invoice_number = note_credit_invoice_number + 1;
            	SET countmin = countmin + 1;
       		END WHILE;

        	SET correlativo := (SELECT note_credit_invoice_number FROM tec_settings);
        	SET result02 := correlativo;
        	SET invoice := (SELECT concat((SELECT SUBSTRING(note_credit_invoice_format,1,5) FROM tec_settings), SUBSTRING(result02, 4)) );
    
    	ELSEIF STRCMP(tipoComProbante, 'B') = 0 THEN
    
    		WHILE countmin < 1 DO
            	UPDATE tec_settings SET note_credit_bill_number = note_credit_bill_number + 1;
            	SET countmin = countmin + 1;
        	END WHILE;

        	SET correlativo := (SELECT note_credit_bill_number FROM tec_settings);
        	SET result02 := correlativo;
        	SET invoice := (SELECT concat((SELECT SUBSTRING(note_credit_bill_format,1,5) FROM tec_settings), SUBSTRING(result02, 4)) );
    
    	END IF;
    
    	INSERT INTO `tec_credit_note` (`sale_id`, `responseCode`, `invoiceTypeCode`, `serieNumero`, `issueDate`, `issueTime`, `documentCurrencyCode`, `customerDocumentID`, `supplierDocumentID`, `customerID`, `customerName`, `taxAmount`, `taxableAmount`, `non_affected`, `exonerated`, `taxSubtotal`, `payableAmount`, `referenceID`, `DocumentTypeCode`, `description`,`user_create`, `date_create`,`estado`) VALUES
(sale_i, responseCod, invoiceTypeCod, invoice, issueDat, issueTim, documentCurrencyCod, customerDocumentI, supplierDocumentI, customerI, customerNam, taxAmoun, taxableAmoun, non_affecte, exonerate, taxSubtota, payableAmoun, referenceI, DocumentTypeCod, descriptio, user_creat, date_creat, estad);
    
    	-- SELECT validarExistencia;
    	SET resultado := (SELECT LAST_INSERT_ID());
    	SET resultSERIEcorre := (invoice);
    ELSE 
    
    	SET resultado := (0);
    	SET resultSERIEcorre := (0);
    
    END IF;
    
END$$
DELIMITER ;


UPDATE `tec_settings` SET `version` = '2.3.3' WHERE `tec_settings`.`setting_id` = 1;

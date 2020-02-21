ALTER TABLE `tec_send_invoice` 
ADD `status_ticket` VARCHAR(140) NULL 
AFTER `observations`; 

DELIMITER $$
CREATE  PROCEDURE `insert_consult_ticket`(IN `descrip` VARCHAR(100), IN `f_name` VARCHAR(100), IN `ticke` VARCHAR(100), IN `codigo` INT)
BEGIN
		UPDATE `tec_send_invoice` SET `status_ticket` = descrip,`status`=codigo WHERE file_name = f_name  AND ticket = ticke;
END $$
DELIMITER ;

UPDATE tec_setting set version= '2.4.2';
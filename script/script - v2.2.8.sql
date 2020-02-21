ALTER TABLE `tec_settings` CHANGE `ticket_format` `nventa_format` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `tec_settings` CHANGE `ticket_number` `nventa_number` INT(11) NOT NULL;

UPDATE `tec_settings` SET `nventa_format` = 'NV001-{0000}' WHERE `tec_settings`.`setting_id` = 1;


UPDATE
tec_send_invoice send
SET send.estado=0;

UPDATE
tec_send_invoice_items send_item
SET send_item.estado=0;

-- cabezera

-- RB
UPDATE
tec_send_invoice send
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='RB' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_invoce=send.id
SET send.estado=1;

-- AB
UPDATE
tec_send_invoice send
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='AB' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_invoce=send.id
SET send.estado=1;

-- AF
UPDATE
tec_send_invoice send
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='AF' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_invoce=send.id
SET send.estado=1;

-- RF
UPDATE
tec_send_invoice send
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='RF' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_invoce=send.id
SET send.estado=1;


-- detalle

-- RB
UPDATE
tec_send_invoice_items send_item
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id as id_detalle
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='RB' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_detalle=send_item.id
SET send_item.estado=1;

-- AB
UPDATE
tec_send_invoice_items send_item
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id as id_detalle
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='AB' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_detalle=send_item.id
SET send_item.estado=1;

-- AF
UPDATE
tec_send_invoice_items send_item
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id as id_detalle
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='AF' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_detalle=send_item.id
SET send_item.estado=1;

-- RF
UPDATE
tec_send_invoice_items send_item
RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id as id_detalle
FROM tec_send_invoice envio 
RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
WHERE envio.id=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id where e.type='RF' GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_detalle=send_item.id
SET send_item.estado=1;

-- UPDATE
-- tec_send_invoice send
-- RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id
-- FROM tec_send_invoice envio 
-- RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
-- WHERE envio.id!=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_invoce=send.id
-- SET send.estado=0;


-- UPDATE
-- tec_send_invoice_items send_item
-- RIGHT JOIN (SELECT sItem.sale_id , sItem.send_invoice_id as id_invoce, envio.status, envio.file_name, sItem.id as id_detalle
-- FROM tec_send_invoice envio 
-- RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
-- WHERE envio.id!=(select max(e.id) from tec_send_invoice e RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id)) tabla2 on tabla2.id_detalle=send_item.id
-- SET send_item.estado=0;

-- SELECT sItem.sale_id , sItem.send_invoice_id, envio.status, envio.file_name, sItem.id
-- FROM tec_send_invoice envio 
-- RIGHT JOIN tec_send_invoice_items sItem on envio.id=sItem.send_invoice_id 
-- WHERE envio.id!=(select max(e.id) 
-- from tec_send_invoice e 
-- RIGHT JOIN tec_send_invoice_items s on s.send_invoice_id=e.id 
-- GROUP by s.sale_id HAVING s.sale_id = sItem.sale_id);

UPDATE `tec_settings` SET `version` = '2.2.8' WHERE `tec_settings`.`setting_id` = 1;
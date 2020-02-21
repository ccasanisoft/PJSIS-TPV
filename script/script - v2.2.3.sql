UPDATE tec_customers SET name = TRIM(name);
UPDATE tec_customers SET cf1 = TRIM(cf1);
UPDATE tec_customers SET cf2 = TRIM(cf2);
UPDATE tec_customers SET phone = TRIM(phone);
UPDATE tec_customers SET email = TRIM(email);
UPDATE tec_customers SET direccion = TRIM(direccion);


ALTER TABLE `tec_registers` CHANGE `total_cash` `total_cash` DECIMAL(25,2) NULL DEFAULT NULL, CHANGE `total_cheques` `total_cheques` DECIMAL(25,2) NULL DEFAULT NULL, CHANGE `total_cc_slips` `total_cc_slips` DECIMAL(25,2) NULL DEFAULT NULL, CHANGE `total_cheques_submitted` `total_cheques_submitted` DECIMAL(25,2) NULL DEFAULT NULL, CHANGE `total_cc_slips_submitted` `total_cc_slips_submitted` DECIMAL(25,2) NULL DEFAULT NULL;


ALTER TABLE `tec_registers` ADD `total_stripe` DECIMAL(25,2) NULL DEFAULT NULL AFTER `total_cc_slips`;

ALTER TABLE `tec_registers` ADD `total_stripe_submitted` DECIMAL(25,2) NULL DEFAULT NULL AFTER `total_cc_slips_submitted`;




UPDATE tec_registers 
INNER JOIN (
		SELECT sum(p.amount) as montoCargar, r.id as idRegistro
		FROM `tec_payments` p
		LEFT JOIN  tec_registers r on r.user_id = p.created_by and p.date >= r.date and p.date <= r.closed_at
		INNER JOIN tec_sales  s on s.id = p.sale_id
		where s.canal_id = 1 and s.estado = 1 and r.status='close' and p.paid_by='stripe' and r.total_stripe is null
		GROUP BY r.id) reemplazo on tec_registers.id = reemplazo.idRegistro
SET tec_registers.total_stripe = reemplazo.montoCargar, tec_registers.total_stripe_submitted = reemplazo.montoCargar
WHERE tec_registers.id = reemplazo.idRegistro;



UPDATE tec_registers 
SET tec_registers.total_stripe = 0.00, tec_registers.total_stripe_submitted = 0.00
WHERE tec_registers.total_stripe is null and `status` = 'close';

UPDATE `tec_settings` SET version='2.2.3';











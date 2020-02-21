
DELIMITER $$
CREATE PROCEDURE `user_datos`()
    NO SQL
BEGIN

        SELECT email, password FROM tec_users;

END$$
DELIMITER ;

UPDATE tec_settings set version='2.3.8';
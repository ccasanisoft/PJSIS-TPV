
UPDATE tec_settings set version='2.3.7';

ALTER TABLE tec_settings

ADD pdf_format int;

UPDATE tec_settings set pdf_format=1;


UPDATE tec_settings SET footer = REPLACE ( footer, '<a <span="" id="selection-marker-1" class="redactor-selection-marker">', '<a id="selection-marker-1" class="redactor-selection-marker">' );
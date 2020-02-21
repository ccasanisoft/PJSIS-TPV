-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tec_document_type`
--

CREATE TABLE `tec_document_type` (
  `id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tec_document_type`
--

INSERT INTO `tec_document_type` (`id`, `document_type`) VALUES
(0, 'DOC.TRIB.NO.DOM.SIN.RUC'),
(1, 'DOCUMENTO NACIONAL DE IDENTIDAD'),
(4, 'CARNET DE EXTRANJERIA'),
(6, 'REGISTRO  UNICO DE CONTRIBUYENTES'),
(7, 'PASAPORTE'),
(8, 'por definir');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tec_document_type`
--
ALTER TABLE `tec_document_type`
  ADD PRIMARY KEY (`id`);
-- Agregando nuevos campos a la tabla `tec_customers`
ALTER TABLE `tec_customers`

 ADD `person_type` varchar(20) NOT NULL,
 ADD `document_type_id` int(11) DEFAULT NULL;

-- modificar la tebla tec_customers

UPDATE tec_customers set person_type=2,document_type_id=6 where cf1=" " and cf2!=" ";

UPDATE tec_customers set person_type=1,document_type_id=1 where cf1!=" ";

UPDATE tec_customers set person_type=0,document_type_id=null where cf1="" and cf2="";

-- registro de VERSIONES
UPDATE `tec_settings` SET `version` = '2.3.6' WHERE `tec_settings`.`setting_id` = 1;

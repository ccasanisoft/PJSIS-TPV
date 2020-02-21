<?php

/**
 * PHP-Class QRCodeGenerator Version 1.0 , released 22-SEP-2013
 * Author: Dror Golan, drorgolan@gmail.com
 *
 * License: GNU GPL (http://www.opensource.org/licenses/gpl-license.html)
 *
 * If you find it useful, you might rate it on http://www.phpclasses.org
 * If you use this class in a productional environment, you may drop me a note, so I can add a link to the page.
 * 
 *  This class uses Google Chart Tools  Infographics for creating a QR code by given criteria as size , type , error correction , encoding and margin.
 *  
 *  
 *  Important: The Infographics portion of Google Chart Tools has been officially deprecated as of April 20, 2012. 
 *  It will continue to work as per Google deprecation policy (https://developers.google.com/chart/terms)
 *
 *  Note from Author : I belive it will remain afterwards based on similar scenarios:-)
 *  
 *  default values 

		data - http://google.com (can be either URL or Text)
		size (In pixels) - 300
		encoding - UTF-8   (Alternative values : Shift_JIS,ISO-8859-1)
		error correction level - L 
		L - [Default] Allows recovery of up to 7% data loss
		M - Allows recovery of up to 15% data loss
		Q - Allows recovery of up to 25% data loss
		H - Allows recovery of up to 30% data loss
		margin  - 4 rows

**/

class QRCodeGenerator {
	/*
	protected $size;
	protected $data;
	protected $encoding;
	protected $errorCorrectionLevel;
	protected $marginInRows;
	protected $debug;
	
	public function __construct($data='http://google.com',$size='300',$encoding='UTF-8',$errorCorrectionLevel='L',$marginInRows=4,$debug=false) {
		
		$this->data=urlencode($data);
		$this->size=($size>10 && $size<1000)? $size : 300;
		$this->encoding=($encoding == 'Shift_JIS' || $encoding == 'ISO-8859-1' || $encoding == 'UTF-8') ? $encoding : 'UTF-8';
		$this->errorCorrectionLevel=($errorCorrectionLevel == 'L' || $errorCorrectionLevel == 'M' || $errorCorrectionLevel == 'Q' || $errorCorrectionLevel == 'H') ?  $errorCorrectionLevel : 'L';
		$this->marginInRows=($marginInRows>0 && $marginInRows<10) ? $marginInRows:4;
	    $this->debug = ($debug==true)? true:false;	
	}
	
	public function generate(){
		
		$QRLink = "https://chart.googleapis.com/chart?cht=qr&chs=".$this->size."x".$this->size. 	           
		           "&chl=" . $this->data . 
		           "&choe=" . $this->encoding .
		           "&chld=" . $this->errorCorrectionLevel . "|" . $this->marginInRows;
		if ($this->debug) echo   $QRLink;         
		return $QRLink;
		
	}*/
		
	protected $direccion;
	protected $nombreArchivo;
	protected $data;
	protected $tamaño;
	protected $level;
	protected $tamañoFondo;
    
    function __construct(){
        $this->direccion="uploads/QR/";
        $this->tamaño=5;
        $this->level='L';
        $this->tamañoFondo=2;
    }
    
    public function generate($nombre, $contenidoQR){
        
                $resultado="";
                
		include ('app/libraries/phpqrcode/qrlib.php');
		//$this->load->library('qrlib');
		$this->nombreArchivo=$this->direccion.$nombre.".png";
		$this->data=$contenidoQR;
		
		if (!file_exists($this->direccion)){
			mkdir($this->direccion);
		}
		
		if(!file_exists($this->nombreArchivo)){
		QRcode::png($this->data, $this->nombreArchivo, $this->level, $this->tamaño, $this->tamañoFondo); 
		}
		
		return $this->direccion.basename($this->nombreArchivo);
	}
	
}
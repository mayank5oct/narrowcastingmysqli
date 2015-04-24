<?php
	date_default_timezone_set('Europe/Amsterdam');
	
	include(__DIR__.'/cPandoraUpdate.php');
	include(__DIR__.'/v4.php');
	include(__DIR__.'/../config.php');
	
	
	
	cPandoraUpdate::f_update();
	
	
	
	
	

?>
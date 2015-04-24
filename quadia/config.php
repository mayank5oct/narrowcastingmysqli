<?php
	
	// Path to directory where all movie files are stored. 
	// The script needs read and write rights in this location
	//cPandoraUpdate::$sFileStorage  = __DIR__.'/storage';
	cPandoraUpdate::$sFileStorage  = __DIR__.'/../videos';
	
	// Path to a log file
	// The script needs write rights in this location
	cPandoraUpdate::$sLogFile      = __DIR__.'/log.txt';
	
	// Quadia OVP domain
	cPandoraUpdate::$sDomainUrl    = 'quadia.webtvframework.com/quadia';	
	
	// Quadia OVP collection id
	cPandoraUpdate::$iCollectionId = 222059;
	
	// Filter videos on bitrate in kilobits
	cPandoraUpdate::$MinBitRate    = 100;
	
	// Filter videos based on vertival resolution (eg. 720 or 1080 for HD)
	cPandoraUpdate::$MinYres       = 100;

	// Array of file and folder names to exclude from checking/delteting
	cPandoraUpdate::$aIgnoreFiles[] = '.DS_Store';
	cPandoraUpdate::$aIgnoreFiles[] = '.gitignore';
	cPandoraUpdate::$aIgnoreFiles[] = 'thumb';

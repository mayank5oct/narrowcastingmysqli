<?php

	/*
		
		domain url
		
		collection id
		
		list all current files in dir
		
		list all entities from collection
		
		loop through entities list
		
			create filename
			
			remove filename from current files list
			
			filename not existst
			
				add file to download list
				
		
		loop through remaining files list
			
			file not in use
			
				add file to remove list
		
		loop through list of files to remove
	
			remove file
			
		loop through missing files list
		
			determine right version
			
			download file
			
			rename downloaded file

	*/
	
	
	class cPandoraUpdate {
		
		static $iCollectionId  = null;
		static $sDomainUrl     = null;
		
		static $sFileStorage  = null;
		static $sLogFile      = null;
		
		static $MinBitRate     = 1000;
		
		static $MinYres        = 720;
		
		static $Codec          = 'h264';
		
		static $sFilenameIdWrapperStart = '_id';
		static $sFilenameIdWrapperEnd   = '_';
		static $sFilenameRemoveCharReg  = '[^a-zA-Z0-9]';
		static $sFilenameReplaceChar    = '_';
		
		static $tmpDir         = '/tmp/cPandoraUpdateConvert';
		
		static $aIgnoreFiles   = array('.','..');
		
		static $aAllFiles      = array();
		static $aAllEntities   = array();
		
		static $aFilesToDelete = array();
		static $aEntitiesToAdd = array();
		
		static $iTimestart     = null;
		
		function f_update(){
		
			self::$iTimestart = microtime(true);
			
			cPandoraUpdate::f_check_environment();
			
			self::fLog(substr('___ Start update ________________________________________________________________', 0, 64));
			
			cPandoraUpdate::f_list_collection();
			cPandoraUpdate::f_list_current_files();
			
			cPandoraUpdate::f_check_entities();
			cPandoraUpdate::f_check_current_files();
			
			cPandoraUpdate::f_delete_files();
			cPandoraUpdate::f_add_new_files();
		
			self::fLog(substr('___ Finished update ('.round(microtime(true) - self::$iTimestart, 2).'s) ________________________________________________________________', 0, 64));
			self::fLog("\n", true);
		}
		
		
		function f_check_environment(){
			$t_aError = array();
			

			if(!isset(self::$iCollectionId)){
				$t_aError[] = 'iCollectionId not set';
			}
			if(!isset(self::$sDomainUrl   )){
				$t_aError[] = 'sDomainUrl not set';
			}
			if(!isset(self::$sFileStorage )){
				$t_aError[] = 'sFileStorage not set';
			}
			if(!isset(self::$sLogFile)){
				$t_aError[] = 'sLogFile not set';
			}

			if(isset(self::$sFileStorage) && !is_readable(self::$sFileStorage)){
				$t_aError[] = 'sFileStorage "'.self::$sFileStorage.'" not readable';
			}
			if(isset(self::$sFileStorage) && !is_writable(self::$sFileStorage)){
				$t_aError[] = 'sFileStorage "'.self::$sFileStorage.'" not writable';
			}
			if(isset(self::$sLogFile) && !is_writable(self::$sLogFile)){
				$t_aError[] = 'sLogFile "'.self::$sLogFile.'" not writable';
			}
			if(!empty($t_aError)){
				die(implode("\n", $t_aError)."\n\n");				
			}
		}
		
		
		function f_list_current_files(){
			
			$t_aDir = scandir(self::$sFileStorage);
			self::$aAllFiles = array();
			
			foreach($t_aDir as $t_sFile){
				if(in_array($t_sFile, self::$aIgnoreFiles)){
					continue;
				}
				
				self::$aAllFiles[] = $t_sFile;
			}
			
			self::fLog('Files local: '.count(self::$aAllFiles).'');
			
		}
		
		function f_list_collection(){
			
			v4::$w_sDomain = self::$sDomainUrl;
			
			$t_aSearch = array(
				  'id' => self::$iCollectionId
			);
			$t_aResult = array();
			
			while(v4::f_search_all(@$i++, $t_aResult, 'asset.simplemovie', $t_aSearch, 'collection')){
				foreach($t_aResult['result']['values'] as $t_aEntity) {
					self::$aAllEntities[$t_aEntity['entity']['id']] = $t_aEntity;
				}
			}
			
			self::fLog('Entities in domain: '.count(self::$aAllEntities).'');
			
		}
		
		function f_check_entities(){
			
			self::$aFilesToDelete = array();
			
			foreach(self::$aAllFiles as $t_sFilename){
				self::$aFilesToDelete[pathinfo($t_sFilename, PATHINFO_FILENAME)]=$t_sFilename;
			}
			
			foreach(self::$aAllEntities as $t_EntityId => $t_aEntity){
				
				$t_sFilename = self::f_create_filename_from_entity($t_aEntity);
				$t_sFilename = self::f_convert_for_filesystem($t_sFilename);
				
				if(isset(self::$aFilesToDelete[$t_sFilename])){
					unset(self::$aFilesToDelete[$t_sFilename]);
				} else {
					self::$aEntitiesToAdd[$t_EntityId] = $t_aEntity;
				}
			}
			
			self::fLog('Entities to add: '.count(self::$aEntitiesToAdd).'');
			self::fLog('Files to delete: '.count(self::$aFilesToDelete).'');
			
		}
		
		function f_check_current_files(){
			foreach(self::$aFilesToDelete as $t_sBasename => $t_sFilename){
				// check if file is in use by TPS
				$t_bFileInUse = false;
				if($t_bFileInUse){
					unset(self::$aFilesToDelete[$t_sBasename]);
					self::fLog('Not deleting "'.$t_sFilename.'": file is in use');
				}
			}
		}
		
		function f_delete_files(){
			foreach(self::$aFilesToDelete as $t_sBasename => $t_sFilename){
				if(is_dir(self::$sFileStorage.'/'.$t_sFilename)){
					self::fLog('Not deleting folder "'.$t_sFilename.'"');
					continue;
				}
				//remove $t_sFilename
				$t_bResult = unlink(self::$sFileStorage.'/'.$t_sFilename);
				if($t_bResult){
					self::fLog('Deleted "'.$t_sFilename.'"');
				} else {
					self::fLog('Error deleting "'.(self::$sFileStorage.'/'.$t_sFilename).'"');
				}
			}
		}
		
		function f_add_new_files(){
			v4::$w_sDomain = self::$sDomainUrl;
			
			$t_aAttributeIds = array();
			foreach(self::$aEntitiesToAdd as $t_iEntityId => $t_aEntity){
				$t_aAttributeIds[$t_iEntityId] = $t_aEntity[$t_aEntity['entity']['type']]['attribute_id'];
			}
			
			$t_aChunk = array_chunk($t_aAttributeIds, 20);
			$t_aAttributes = array();
			foreach($t_aChunk as $t_iChnk => $t_aIds){
				$t_aResult = v4::fV4Call('entity', 'get_multiple', array('domainrange' => 'SD', 'id' => array_values($t_aIds)));
				foreach($t_aResult['result']['values'] as $t_aAttVideo){
					$t_aAttributes[$t_aAttVideo['entity']['id']] = $t_aAttVideo;
				}
			}

			$t_aFiles = array();
			$t_aNoVersion = array();
			foreach($t_aAttributeIds as $t_aAssetId => $t_aAttributeId){
				if(!isset($t_aAttributes[$t_aAttributeId])){
					self::fLog('Attribute '.$t_aAttributeId.' not found');
					continue;
				}
				foreach($t_aAttributes[$t_aAttributeId]['attribute_video'] as $t_aVersion){
					if(
						    !isset($t_aFiles[$t_aAssetId])
					//	&& !$t_aVersion['source']
						&&  $t_aVersion['video']['bitrate'] >= (cPandoraUpdate::$MinBitRate*1000)
						&&  $t_aVersion['video']['stream'][$t_aVersion['video']['video_stream_id']]['height']      >= cPandoraUpdate::$MinYres
						&&  $t_aVersion['video']['stream'][$t_aVersion['video']['video_stream_id']]['video_codec'] == cPandoraUpdate::$Codec
					) {
						$t_aFiles[$t_aAssetId] = array(
							  'url' => str_replace(' ', '%20', $t_aVersion['video']['url'])
							, 'ext' => pathinfo($t_aVersion['video']['filename'], PATHINFO_EXTENSION)
						);
					}
				}
				if(!isset($t_aFiles[$t_aAssetId])){
					$t_aNoVersion[$t_aAssetId] = $t_aAttributeId;
				}
			}
			
			if(!empty($t_aNoVersion)){
				self::fLog('No versions found for entities: '.implode(', ', $t_aNoVersion));
			}
			
			if(!empty($t_aFiles)) {
				self::fLog('Downloading '.count($t_aFiles).' new files for entities: '.implode(', ', array_keys($t_aFiles)));
			}
			
			foreach($t_aFiles as $t_iAssetId => $t_aFile) {
				$t_sNewFilename = self::f_create_filename_from_entity(self::$aEntitiesToAdd[$t_iAssetId]).'.'.$t_aFile['ext'];
				
				$t_iTime = microtime(true);
				self::fLog('Start downloading from "'.$t_aFile['url'].'" ');
				
				$fp = fopen(self::$sFileStorage.'/'.$t_sNewFilename, 'w');
				$ch = curl_init($t_aFile['url']);
				
				curl_setopt($ch, CURLOPT_FILE, $fp);
				
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);
				
				self::fLog('Finished downloading as "'.$t_sNewFilename.'"  ('.round(microtime(true)-$t_iTime, 2).'s)');
				
			}
		}
		
		private function f_convert_for_filesystem($p_sFilename){
			$t_sPath = self::$tmpDir.'/'.md5($p_sFilename);
			exec('mkdir -p '.$t_sPath);
			file_put_contents($t_sPath.'/'.$p_sFilename,'');
			$t_aFile = scandir($t_sPath);
			return $t_aFile[2];
		}
		
		private function f_create_filename_from_entity($p_aEntity){
			
			$t_sTitle = current(current($p_aEntity['meta']['title']));
			$t_sTitle = trim($t_sTitle['value']);
			if(empty($t_sTitle)) {
				$t_sTitle = 'untitled';
			}

			// remove filesystem unsafe characters (only "/" for unix systems)
			$t_sTitle = preg_replace('/'.self::$sFilenameRemoveCharReg.'/', self::$sFilenameReplaceChar, $t_sTitle);
			
			return $t_sTitle.self::$sFilenameIdWrapperStart.$p_aEntity['entity']['id'].self::$sFilenameIdWrapperEnd;
		}
		
		private function f_get_entity_id_from_filename($p_sFilename){
			
			$t_aMatch = array();
			$t_rReg = '/'.preg_quote(self::$sFilenameIdWrapperStart, '/').'([0-9]+)'.preg_quote(self::$sFilenameIdWrapperEnd, '/').'/';
			$t_uResult = preg_match($t_rReg, $p_sFilename, $t_aMatch);
			
			if(isset($t_aMatch[1])) {
				return $t_aMatch[1];
			}
			
			return false;			
		}
		
		private function fLog($p_sTxt, $p_bAppend=false){
			if($p_bAppend){
				$t_sLine = $p_sTxt;
			}else{
				$t_sLine = ($p_bAppend?'':"\n".date(DATE_ATOM).' ').$p_sTxt;
			}
			echo $t_sLine;
			file_put_contents(self::$sLogFile, $t_sLine, FILE_APPEND|LOCK_EX);
		}
		
	};



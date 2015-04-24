<?php
/*
    $t_aResult = v4::fV4Call('user', 'login', array(
          'username' => 'demo_user'  
        , 'password' => 'DQW9oieTR'
    ));
    echo '<h1>result from user.login</h1>';
    echo '<pre>';
    print_r($t_aResult);
    echo '</pre>';
    
*/
	/*
        Example of a basic class to call the Quadia OVP API
    */
    class v4 {
        static $w_sDomain = 'quadia.webtvframework.com/demo';
        // default options
        static $w_aDefCurlOpt = array(
              CURLOPT_RETURNTRANSFER => true
            , CURLOPT_COOKIESESSION  => true
            , CURLOPT_COOKIEJAR      => '/tmp/cookies.txt'
            , CURLOPT_COOKIEFILE     => '/tmp/cookies.txt'
            , CURLOPT_POST           => true
        );
        static function fV4Call($p_sSubject, $p_sFunction, $p_aPost = array()){
            // construct the API function URL
            $t_sCall = self::f_create_curl_url($p_sSubject, $p_sFunction);
            // create the curl object
            $t_oCurl = curl_init($t_sCall);
            // combine curl options with arguments, formatted for POST
            $t_aCurlOpt = self::$w_aDefCurlOpt;
            $t_aCurlOpt[CURLOPT_POSTFIELDS] = self::f_create_curl_params($p_aPost);
            // apply all options for curl
            curl_setopt_array($t_oCurl, $t_aCurlOpt);
            // execute curl
            $t_sResult = curl_exec($t_oCurl);
        	//check http status code
            if(curl_getinfo($t_oCurl, CURLINFO_HTTP_CODE) != 200){
            	die("\n".'Processing died, could not connect to "'.self::$w_sDomain.'"'."\n");
            }
            // decode the result using JSON into an associative array
            $t_aResult = json_decode($t_sResult, true);
            //check v4 status code
            if($t_aResult['code']===-102&&$t_aResult['subject']!=='user'){
            	die("\n".'Processing died, clog in required'."\n");
            }
            
            // close curl connection
            curl_close($t_oCurl);
            
            // return result
            return $t_aResult;
        }
        /*
			usage:
			while(v4::f_search_all(@$i++, $t_aResult, 'domain', $t_aSearch)){
				
			}
        */
        static function f_search_all($p_iCounter, &$p_rOutput=array(), $p_sSubject='entity', &$p_aParam=array(), $t_sType='search'){
			$t_aResult = self::fV4Call($p_sSubject, $t_sType, $p_aParam);
			$p_aParam['offset'] = (($p_iCounter+1)*$t_aResult['result']['summary']['limit']);// $t_aResult['result']['summary']['offset'] + $t_aResult['result']['summary']['limit'];
			$p_rOutput = $t_aResult;
			
			return !(empty($t_aResult['result']['values']));
		}

		static function f_search_latest($p_iAmount, $p_sSubject, $t_aSearch){
			$t_aSearch['limit'] = 1;
			$t_aResult = v4::fV4Call($p_sSubject, 'search', $t_aSearch);
			$t_aSearch['limit']  = $p_iAmount;
			$t_aSearch['offset'] = max($t_aResult['result']['summary']['total'] - $t_aSearch['limit'], 0);
			
			return v4::fV4Call($p_sSubject, 'search', $t_aSearch);
		}

        
        static function f_create_curl_url($p_sSubject, $p_sFunction){
            // check if a domain is set
            if(empty(self::$w_sDomain)){
                die('no domain');
            }
            return 'https://'.self::$w_sDomain.'/_function/'.$p_sSubject.'.'.$p_sFunction;
        }
        static function f_create_curl_params($p_aPost){
            $r_aParam = array();
            self::http_build_query_for_curl($p_aPost, $r_aParam);
            return $r_aParam;
        }
        static function f_create_curl_params_as_string($p_aPost){
            $t_aParam = self::f_create_curl_params($p_aPost);
            $r_sOutput = '';
            foreach($t_aParam as $t_sKey => $t_sVal){
            	$t_sKey = urlencode($t_sKey);
            	$t_sKey = str_replace(array('%5B', '%5D'), array('[',']'),$t_sKey);
            	$r_sOutput.= '&'.$t_sKey.'='.urlencode($t_sVal);
            }
            return substr($r_sOutput,1);
        }
        
        // Function to format an multi-dimensional array as a a flat associative array
        static function http_build_query_for_curl($arrays=array(), &$new=array(), $prefix=null){
            if(is_object($arrays)){
                $arrays=get_object_vars($arrays);
            } else if (!is_array($arrays)){
            	$arrays = (array)$arrays;
            }
            foreach($arrays as $key=>$value){
                $k = isset( $prefix ) ? $prefix.'['.$key.']' : $key;
                if(is_array($value) || is_object($value)){
                    self::http_build_query_for_curl($value, $new, $k);
                } else {
                    $new[$k]=$value;
                }
            }
            return $new;
        }
    }


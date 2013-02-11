<?php




define('MF_API_ID', 'YOUR_API_ID');
define('MF_API_SECRET', 'YOUR_API_SECRET');
	
	
	
class morguefile {

	function __construct() {
		if(!function_exists('curl_init')){
			throw new Exception('Curl is required for morguefile API');
		}
	}
	
	public function call($parms, $method='json'){
		$o = $this->cleanParamString($parms);
		if(!empty($o)){
		
			if($method!='json' && $method!='xml'){
				$method = 'json';
			}
			/* create the signature */
			$sig = hash_hmac("sha256", $o['str'], MF_API_SECRET);
			/* create the api call */
			$c = curl_init ('https://morguefile.com/api/' . $o['uri'] . '.'.$method );
			curl_setopt ($c, CURLOPT_POST, true);
			curl_setopt ($c, CURLOPT_POSTFIELDS, 'key='.MF_API_ID.'&sig='.$sig);
			curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
			$page = curl_exec ($c);
			curl_close ($c);			
			if(!empty($page)){
				if($method=='json'){
					$data = json_decode($page);
				} else {
					$data = ($page);
				}
				return $data;
			} else {
				throw new Exception(curl_error($ch));
			}
		} else {
			throw new Exception('Malformed string');
		}
	}
	
	private function cleanParamString($parms){
		/* clean up the url string to avoid errors */
		$parms = trim(strtolower($parms));
		$p = explode('/', $parms);
		$p = array_filter($p, 'strlen');
		if(!empty($p)) {
			$o['str'] = implode('', $p);
			$o['uri'] = implode('/', $p) . '/';
			return $o;
		}
	}
}
	
?>
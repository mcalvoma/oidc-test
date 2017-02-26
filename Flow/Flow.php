<?php
include_once 'utils/config.php';
include_once 'PublicKey.php';
include_once 'Token/IDToken.php';
include_once 'Token/AccessToken.php';

abstract class Flow { 


	function __construct() {
		$this->publicKey = $this->requestPublicKey();
	}

	// Function that requests the public key and stores it in an object.
	private function requestPublicKey () {
		global $config;
		
		$url = $config['IdP']['iss'] . $config['openid']['jwk_path'];
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		
		if(!isset($response->keys)){
			throw new Exception('Failed to retrieve public key: ' . json_encode($response));
		} else {
			
			$results = array_filter($response->keys, function($key) {
				return $key->kty == "RSA";
			})[0];

			if(!isset($results->alg) || !isset($results->n) || !isset($results->e)){
				throw new Exception('Failure of parameters when receiving the public key.: ' . json_encode($results));
			}

			$n = base64_decode(str_replace(['-','_'], ['+','/'], $results->n));
			$e = base64_decode($results->e);
			
			return new PublicKey($n, $e, $results->alg);			
		}
	}

	public function getPublicKey(){
    	return $this->publicKey;
    }

    function getIDToken() {
		return $this->id_token;
	}
	
	function getAccessToken(){
		return $this->access_token;
	}
}

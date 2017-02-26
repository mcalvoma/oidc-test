<?php
include_once 'utils/config.php';
include_once 'Flow.php';


class CodeFlow extends Flow { 
    

	function __construct($state, $code) {
		parent::__construct();
		$this->state = $state;
		$this->code = $code;
		$this->getTokens($code);
	}

	
	// Function that performs a request using CURL to request a Token ID and an Access Token with an Authorization Code.
	private function getTokens($code) {

		global $config;
				
		$a_token =  $config['IdP']['iss'] . $config['openid']['accessToken_path'];

		$post = [
			'code' => $code,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $config['request']['redirect_c'],
		];
					
		$ch = curl_init($a_token);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, TRUE); 
		curl_setopt($ch, CURLOPT_USERPWD, $config['request']['client_id'] . ":" . $config['request']['client_secret']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		
		$this->processParams($response);
	}


	// Function that processes the parameters received from the request of the tokens. Create Access Token and Token ID objects with them.
	private function processParams($response) {

		if 	((isset($response->error)) || (!isset($response->scope)) ||
			(!isset($response->token_type)) || (!isset($response->expires_in)) ||
			(!isset($response->id_token)) || (!isset($response->access_token))) {
			throw new Exception('Failed to retrieve the auth response: ' . json_encode($response));
		}

		$this->access_token	= new AccessToken($response->access_token, $this->state, "Code Flow");
		$this->id_token 	= new IDToken($response->id_token);
		$this->token_type 	= $response->token_type;
		$this->expires_in 	= $response->expires_in;
		$this->scope 		= $response->scope;
	}
	
	function getIDToken() {
		return $this->id_token;
	}
	
	function getAccessToken(){
		return $this->access_token;
	}
	 
}

?> 
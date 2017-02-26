<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include_once 'phpseclib/Crypt/RSA.php';
include_once 'utils/config.php';
include_once 'Token.php';

class IDToken extends Token { 


	// START CONSTRUCT.
	function __construct($idToken) {

		$this->id_token = $idToken;

		$cutIDT = explode(".", $this->id_token);
		if(sizeof($cutIDT) != 3){
			throw new Exception('Invalid ID Token: ' . $this->id_token);
		}
		
		self::processHeader(json_decode(base64_decode($cutIDT[0])));
		self::processPayload(json_decode(base64_decode($cutIDT[1])));
		$this->sign = $cutIDT[2];

	}	

	public function getAlg(){
		return $this->header['alg'];
	}

	public function getHash(){
		return $this->payload['at_hash'];
	}

	// Process and save the header data of a received Token ID.
	private function processHeader ($header) {
		if 	((!isset($header->typ)) || (!isset($header->alg)) ||
			(!isset($header->kid))) {
			throw new Exception('Invalid ID Token header: ' . $header);
		} else {
			$this->header['typ'] = $header->typ;
			$this->header['alg'] = $header->alg;
			$this->header['kid'] = $header->kid;
		}
	}
	
	// Process and save the payload data of a received Token ID.
	private function processPayload ($payload) {
		if 	((!isset($payload->tokenName)) || (!isset($payload->azp)) ||
			(!isset($payload->sub)) || (!isset($payload->at_hash))  ||
			(!isset($payload->iss)) || (!isset($payload->iat))  ||
			(!isset($payload->auth_time)) || (!isset($payload->exp))  ||
			(!isset($payload->tokenType)) || (!isset($payload->nonce))  ||
			(!isset($payload->realm)) || (!isset($payload->aud))) {
			throw new Exception('Invalid ID Token payload: ' . json_encode($payload));
		} else {
			$this->payload['tokenName'] = $payload->tokenName;
			$this->payload['azp'] = $payload->azp;
			$this->payload['sub'] = $payload->sub;
			$this->payload['at_hash'] = $payload->at_hash;
			$this->payload['iss'] = $payload->iss;
			$this->payload['iat'] = $payload->iat;
			$this->payload['auth_time'] = $payload->auth_time;
			$this->payload['exp'] = $payload->exp;
			$this->payload['tokenType'] = $payload->tokenType;
			$this->payload['nonce'] = $payload->nonce;
			$this->payload['realm'] = $payload->realm;
			$this->payload['aud'] = $payload->aud;
		}
	}
	// END CONSTRUCT.



	// START VALIDATION FUNCTIONS.
	public function validateISS(){
		global $config;
		return 	(isset($this->payload['iss']))
				&& ($this->payload['iss'] == $config['IdP']['iss']);

	}
	
	public function validateAUD(){
		global $config;
		return 	(isset($this->payload['aud']))
				&& (in_array($config['request']['client_id'],$this->payload['aud']));

	}
	
	public function validateAZP(){
		global $config;
		if ((sizeof($this->payload['aud'])) > 1) {
			return (isset($this->payload->azp)) && ($config['request']['client_id'] == $this->payload->azp);
		}
		return 	true; 
	}
	
	public function validateEXP(){
		return 	time() <= $this->payload['exp']; 
	}
	
	public function validateIAT(){	
		return 	time()+1 >= $this->payload['iat']; 
	}
	
	public function validateNONCE(){	
		return 	(isset($_SESSION["nonce"])) && (isset($this->payload['nonce'])) && ($_SESSION["nonce"] == $this->payload['nonce']); 
	}
	
	public function validateTime(){	
		global $config;
		return 	(time() + $config['other']['accepted_time_idt']) >= $this->payload['iat']; 
	}
	
	public function validateSignature($publicKey){
		
		$keySignature = str_replace(['-','_'], ['+','/'], $this->sign);
		$keySignature = base64_decode($keySignature);
			
		$alg = self::getEncryptionAlgorithm($publicKey->getAlg());
		$hash = 'sha'.$alg;
		
		$rsa = new Crypt_RSA();
		$rsa->setHash($hash);
		$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$rsa->loadKey([
			'n' => new Math_BigInteger($publicKey->getN(), $alg),
			'e' => new Math_BigInteger($publicKey->getE(), $alg)
		]);
		
		$data = explode(".", $this->id_token);
		$data = $data[0].".".$data[1];
		return $rsa->verify($data, $keySignature);
	}
	// END VALIDATION FUNCTIONS.

	

	public function __toString(){
		$string 	 = '{ \n';
		$string 	.= '\t "id_token": "'.$this->id_token.'",\n';
		
		$string 	.= '\t "header": {\n';
		$string 	.= '\t\t "typ": "'.$this->header['typ'].'",\n';
		$string 	.= '\t\t "alg": "'.$this->header['alg'].'",\n';
		$string 	.= '\t\t "kid": "'.$this->header['kid'].'"\n';
		$string 	.= '\t }, \n';
		
		$string 	.= '\t "payload": {\n';
		$string 	.= '\t\t "tokenName": "'.$this->payload['tokenName'].'",\n';
		$string 	.= '\t\t "azp": "'.$this->payload['azp'].'",\n';
		$string 	.= '\t\t "sub": "'.$this->payload['sub'].'",\n';
		$string 	.= '\t\t "at_hash": "'.$this->payload['at_hash'].'",\n';
		$string 	.= '\t\t "iss": "'.$this->payload['iss'].'",\n';
		$string 	.= '\t\t "iat": "'.$this->payload['iat'].'",\n';
		$string 	.= '\t\t "auth_time": "'.$this->payload['auth_time'].'",\n';
		$string 	.= '\t\t "exp": "'.$this->payload['exp'].'",\n';
		$string 	.= '\t\t "tokenType": "'.$this->payload['tokenType'].'",\n';
		$string 	.= '\t\t "nonce": "'.$this->payload['nonce'].'",\n';
		$string 	.= '\t\t "realm": "'.$this->payload['realm'].'",\n';
		$string 	.= '\t\t "aud": '.json_encode($this->payload['aud']).',\n';
		$string 	.= '\t }, \n';

		$string 	.= '\t "sign": "'.$this->sign.'"\n';

		$string 	.= '}'.'\n';
		return $string;
    }
}

?>
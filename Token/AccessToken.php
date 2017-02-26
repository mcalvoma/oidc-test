<?php
include_once 'utils/config.php';
include_once 'Token.php';

class AccessToken extends Token{ 

	function __construct($accessToken, $state, $infoType) {
		$this->state = $state;
		$this->access_token = $accessToken;
		$this->info = $infoType;
	}


	// START VALIDATION FUNCTIONS.
	function validateHash($hash, $algorithm){

		$bit_alg = parent::getEncryptionAlgorithm($algorithm);
		$len = ((int)$bit_alg)/16;
		$hash_accessToken = parent::urlEncode(substr(hash('sha'.$bit_alg, $this->access_token, true), 0, $len));

		return $hash == $hash_accessToken;
	}

	function validateState(){
		return ((isset($_SESSION["state"])) && ($_SESSION["state"] == $this->state));
	}
	// END VALIDATION FUNCTIONS.


	public function __toString(){
		$string 	 = '{ \n';
		$string 	.= '\t "info": "'.$this->info.'"\n';
		$string 	.= '\t "access_token": "'.$this->access_token.'"\n';
		$string 	.= '\t "state": "'.$this->state.'"\n';
		$string 	.= '}'.'\n';
		return $string;
    }
}

?>
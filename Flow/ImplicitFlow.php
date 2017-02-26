<?php
include_once 'Flow.php';

class ImplicitFlow extends Flow { 
	

	//Save all data and create Access Token and Token ID objects with them.
	function __construct($idToken, $accessToken, $tokenType, $expires, $scope, $state) {
		parent::__construct();
		$this->access_token	= new AccessToken($accessToken, $state, "Implicit Flow");
		$this->id_token 	= new IDToken($idToken);
		$this->state = $state;
		$this->token_type 	= $tokenType;
		$this->expires_in 	= $expires;
		$this->scope 		= $scope;
	}	

}
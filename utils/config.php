<?php

$config = [
    'IdP' => [
		'iss' => 'https://sample.idp.com/openam/oauth2', 			// Put the URL of your OIDC IdP.
    ],
    'request' => [
        'scope_params' => 'openid email', 							// Put the scope you want to retrieve.
        'client_id' => 'myRP',										// Enter the identifier assigned to this RP.
        'client_secret' => 'test',									// Enter the password assigned to this RP.
		'redirect_c' => 'https://myRP.com/oidc-test',				// Put the redirect URI in the Authorization Code flow.
		'redirect_i' => 'https://myRP.com/oidc-test',				// Put the redirect URI in the implicit flow.
    ],
	'openid' => [													// Common URIs of the OpenID Connect protocol.
		'auth_path' => '/authorize',
        'accessToken_path' => '/access_token',
        'userInfo_path' => '/userinfo',
		'discover_path' => '/.well-known/webfinger',
		'jwk_path' => '/connect/jwk_uri',
	],
	'other' => [
		'accepted_time_idt' => 22224109								// Token ID validity time (in milliseconds).						
	]
];


$language = [ 														// Application text. Write in your language.
    'page' => [
		'title' 				=> 'OpenAM OIDC - Test',
		'button_implicit' 		=> 'Test Implicit Flow',
		'button_code' 			=> 'Test Code Flow',
		'title_at_validation'	=> 'Access Token validation',
		'title_idt_validation'	=> 'ID Token validation',
		'title_at_data'			=> 'Access Token data',
		'title_idt_data'		=> 'ID Token data',
		'validation_ok'			=> 'Pass',
		'validation_bad'		=> 'Not passed',
		'col_data'				=> 'Data',
		'col_validation'		=> 'Validation',
	],
];

?>
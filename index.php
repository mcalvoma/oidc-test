<?php
include_once 'utils/config.php';
include_once 'utils/utils.php';
include_once 'Flow/CodeFlow.php';
include_once 'Flow/ImplicitFlow.php';

// If the nonce and state parameters do not exist in session , they are created.
session_start();
if(!isset($_SESSION['nonce']) || !isset($_SESSION['state'])){
	$_SESSION["nonce"] = generateRandString();
	$_SESSION["state"] = generateRandString();
}

// Prepare Authentication Request (Flow Authorization Code)
$code_request_url 	= $config['IdP']['iss'] . $config['openid']['auth_path']
					. "?response_type=code"
					. "&scope=" . $config['request']['scope_params']
					. "&client_id=" . $config['request']['client_id']
					. "&state=" . $_SESSION["state"]
					. "&nonce=" . $_SESSION["nonce"]
					. "&redirect_uri=" . $config['request']['redirect_c'];

// Prepare Authentication Request (implicit flow).
$implicit_request_url 	= $config['IdP']['iss'] . $config['openid']['auth_path']
						. "?response_type=token id_token"
						. "&scope=" . $config['request']['scope_params']
						. "&client_id=" . $config['request']['client_id']
						. "&state=" . $_SESSION["state"]
						. "&nonce=" . $_SESSION["nonce"]
						. "&redirect_uri=" . $config['request']['redirect_i'];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $language['page']['title']; ?></title>
    <meta name="description" content="OpenAM OIDC - Test">
    <meta name="author" content="mcalvoma">
    <script type="text/javascript" src="///code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="assets/implicit.js"></script>								<!-- Very important in the implicit flow if OpenAM is used. -->
</head>

<body>
	<h1><?php echo $language['page']['title']; ?></h1>
	<p><a href="<?php echo $code_request_url ?>"><?php echo $language['page']['button_code']; ?></a></p>
	<p><a href="<?php echo $implicit_request_url ?>"><?php echo $language['page']['button_implicit']; ?></a></p>
	</br>
<?php

	// Code Flow.
	if (isset($_GET['code'])) { 

		// This is done to see which parameters fail in validation and which do not. In a real environment, if any of these parameters are not received, access should be invalid.
		$state = (isset($_GET['state'])) ? $_GET['state'] : "";

		// Flow/CodeFlow.php
		$objCodeFlow = new CodeFlow($state, $_GET['code']);

		// The values are saved in the session variables.
		$_SESSION["id_token"] = $objCodeFlow->getIDToken();
		$_SESSION["access_token"] = $objCodeFlow->getAccessToken();
		$_SESSION["public_key"] = $objCodeFlow->getPublicKey();

		// Redirect to page that checks tokens.
		header('Location: admin.php');
	

	// Implicit Flow.
	} else if (isset($_GET['id_token'])) {

		// This is done to see which parameters fail in validation and which do not. In a real environment, if any of these parameters are not received, access should be invalid.
		$state = (isset($_GET['state'])) ? $_GET['state'] : "";
		$access_token = (isset($_GET['access_token'])) ? ($_GET['access_token']) : "";
		$expires_in = (isset($_GET['expires_in'])) ? ($_GET['expires_in']) : "";
		$scope = (isset($_GET['scope'])) ? ($_GET['scope']) : "";
		$token_type = (isset($_GET['token_type'])) ? ($_GET['token_type']) : "";

		// Flow/ImplicitFlow.php
		$objImplicitFlow = new ImplicitFlow($_GET['id_token'], $access_token, $token_type, $expires_in, $scope, $state);

		//The values are saved in the session variables.
		$_SESSION["id_token"] = $objImplicitFlow->getIDToken();
		$_SESSION["access_token"] = $objImplicitFlow->getAccessToken();
		$_SESSION["public_key"] = $objImplicitFlow->getPublicKey();

		// Redirect to page that checks tokens.
		header('Location: admin.php');
	}
?>
</body>
</html>
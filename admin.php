<?php
include_once 'utils/config.php';
include_once 'utils/utils.php';
include_once 'Token/IDToken.php';
include_once 'Token/AccessToken.php';
include_once 'Flow/PublicKey.php';

session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $language['page']['title']; ?></title>
    <meta name="description" content="<?php echo $language['page']['title']; ?>">
    <meta name="author" content="mcalvoma">
	<style>
		table, th, td {border: 1px solid black; max-width:100%;}
		.left{min-width:300px;}
	</style>
</head>

<body>
	<h1><?php echo $language['page']['title']; ?></h1>
	</br>
<?php

	 	
	if (isset($_SESSION["id_token"]) && isset($_SESSION["access_token"]) && isset($_SESSION["public_key"])) { 

		// Access Token validation.
		$hash = $_SESSION["id_token"]->getHash();
		$alg = $_SESSION["id_token"]->getAlg();
		$validationAT["State Code"] = $_SESSION["access_token"]->validateState();
		$validationAT["Hash Access Token"] = $_SESSION["access_token"]->validateHash($hash, $alg);
		
		// ID Token validation.
		$validationIdT["ISS ID Token"] 	 		= $_SESSION["id_token"]->validateISS();
		$validationIdT["AUD ID Token"] 	 		= $_SESSION["id_token"]->validateAUD();
		$validationIdT["AZP ID Token"] 	 		= $_SESSION["id_token"]->validateAZP();
		$validationIdT["EXP ID Token"] 	 		= $_SESSION["id_token"]->validateEXP();
		$validationIdT["IAT ID Token"] 	 		= $_SESSION["id_token"]->validateIAT();
		$validationIdT["NONCE ID Token"] 		= $_SESSION["id_token"]->validateNONCE();
		$validationIdT["Acepted Time ID Token"] = $_SESSION["id_token"]->validateTime();
		$validationIdT["Signature ID Token"] 	= $_SESSION["id_token"]->validateSignature($_SESSION["public_key"]);
		
?>	
		<table>
			<tr>
				<th><h1><?php echo $language['page']['col_validation'] ?></h1></th>
				<th><h1><?php echo $language['page']['col_data'] ?></h1></th>
			</tr>
			<tr>
			<td class="left">
<?php	
				echo "<h2>".$language['page']['title_at_validation'].":</h2>";
				echo printValidation($validationAT);

				echo "<h2>".$language['page']['title_idt_validation'].":</h2>";
				echo printValidation($validationIdT);			
?>		
			</td>
			<td>
			<h2><?php echo $language['page']['title_at_data']; ?></h2>
<?php 
			echo phpToHTML($_SESSION["access_token"]);
?>
			<h2><?php echo $language['page']['title_idt_data']; ?></h2>
<?php 
			echo phpToHTML($_SESSION["id_token"]);
?>		
			</td>
		  </tr>
		</table>
		
<?php
			
	} else {
		header('Location: index.php');
	}
?>

</body>
</html>

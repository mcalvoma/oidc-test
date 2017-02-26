<?php
	
	function printValidation($validation){
		$string = "";
		foreach($validation as $c => $v){
			$string .= "<p>{$c}: <span style='color:";
			$string .= ($v) ? "green;'>Pass" :"red;'>Not passed";
			$string .= "</span></p>";
		}
		return $string;
	}

	function phpToHTML ($toPrint){
		return str_replace(['\n', '\t'], ['</br>', '&nbsp&nbsp&nbsp'], $toPrint);
	}

	function generateRandString() {
        return md5(uniqid(rand(), TRUE));
    }
?>
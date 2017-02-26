<?php

class PublicKey {

	function __construct($n, $e, $alg) {
		$this->e = $e;
		$this->n = $n;
		$this->alg = $alg;
	}

	function getE() {
		return $this->e;
	}

	function getN() {
		return $this->n;
	}

	function getAlg() {
		return $this->alg;
	}

	public function __toString(){
		$string 	 = "{"."\n";
		$string 	.= "\talg: ".$this->alg."\n";
		$string 	.= "\te: ".$this->e."\n";
		$string 	.= "\tn: ".$this->n."\n\n";
		$string 	.= "}"."\n";
		return $string;
    }

}	
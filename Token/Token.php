<?php

class Token {

    // Parsing the encryption algorithm.
    protected function getEncryptionAlgorithm($alg){
        switch ($alg) {
        case 'RS256':
        case 'RS384':
        case 'RS512':
            $hashtype = substr($alg, 2);
            break;
        default:
            throw new Exception('No support for signature type: ' . $alg);
        }
        return $hashtype;
    }

    // Hash parsing to remove strange characters.
    protected function urlEncode($str) {
        $enc = base64_encode($str);
        $enc = rtrim($enc, "=");
        $enc = strtr($enc, "+/", "-_");
        return $enc;
    }

}

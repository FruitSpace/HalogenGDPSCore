<?php

class ThunderRSA{

	private $keyLen, $privKey, $pubKey, $originCert;

	function __construct($keyLen=2048){ //When using radiance we are permanently corrupting core keys
		if($keyLen!=1024 and $keyLen!=2048 and $keyLen!=4096){$keyLen=2048;}
		$this->keyLen=$keyLen;
	}

	function insertKeys($publicKey=null, $privateKey=null){
		$this->pubKey=$publicKey;
		$this->privKey=$privateKey;
	}

	function getPrivateKey(){
		return $this->privKey;
	}

	function getPublicKey(){
		return $this->pubKey;
	}

	function genPrivateKey(){
		$axDelta = openssl_pkey_new(array(
			'private_key_bits' => $this->keyLen,
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
			'digest_alg' => 'sha512'
		));
		openssl_pkey_export($axDelta, $this->privKey);
		$axPubKey=openssl_pkey_get_details($axDelta);
		$this->pubKey=$axPubKey["key"];
	}

	function encrypt($plain){
		openssl_public_encrypt($plain,$enc, $this->pubKey);
		return $enc;
	}

	function decrypt($block){
		openssl_private_decrypt($block, $dec, $this->privKey);
		return $dec;
	}
}
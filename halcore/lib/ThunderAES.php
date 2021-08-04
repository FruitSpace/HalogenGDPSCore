<?php
/*
==========================
M41den 2021 Copyright
==========================
ThunderAES Module
*/

require_once 'logger.php';  //We need logger for everything

class ThunderAES{
    //Declare AES bits(128,256,512), cipher, iv length, iv key, tag name, tag length
    private $bittag,$cipher,$ivlen,$iv,$key,$tag,$tag_len;

    function __construct($bittag=256,$taglength=16){
        if($taglength<4 or $taglength>16){$taglength=16;} //You can set your own tag length
        if($bittag!=128 and $bittag!=256 and $bittag!=512){$bittag=256;} //you can choose strength
        $this->tag_len=$taglength; //Declare tag length
        $this->bittag=$bittag; //Declare local bittag
        $this->cipher="aes-".$this->bittag."-gcm"; //More secure than CBC
        $this->ivlen=openssl_cipher_iv_length($this->cipher); //AutoLength
    }

    function encrypt($plain){
        $this->iv=openssl_random_pseudo_bytes($this->ivlen); //AutoGen IV
        $ciphertext_raw=openssl_encrypt($plain,$this->cipher,$this->key,OPENSSL_RAW_DATA,$this->iv,$this->tag); //encrypt
        if($ciphertext_raw==false){
            $former="Encryption error on [$plain]"; //Just generate log...
            err_handle("ThunderAES","err",$former);
            return false;
        }
        return base64_encode($this->iv.$this->tag.$ciphertext_raw); //Return AIO text base64
    }

    function decrypt($block){
        $rawblock=base64_decode($block); //Prepare raw block
        $this->iv=substr($rawblock,0,$this->ivlen); //Extract IV key
        $this->tag=substr($rawblock,$this->ivlen,$this->tag_len); //Extract Tag
        $ciphertext_raw=substr($rawblock,$this->ivlen+$this->tag_len); //Extract Encrypted_block
        $plain=openssl_decrypt($ciphertext_raw,$this->cipher,$this->key,OPENSSL_RAW_DATA,$this->iv,$this->tag); //Decrypt
        if($plain==false){
            $former="Decryption error on [$block]\n\tKey: $this->key"; //Just generate log...
            err_handle("ThunderAES","err",$former);
            return false;
        }
        return $plain; //Return plain text
    }

    function genkey($pass,$len=32){
        $this->key = substr(hash('sha256', $pass, true), 0, $len); //Here we just get hash from password and cut it to length
    }
}
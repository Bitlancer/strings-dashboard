<?php

class UserKeysController extends AppController {

    public function generateKeyPair(){

        $this->autoRender = false;

        /*
        if(!$this->request->is('post'))
            throw new BadRequestException('Only the HTTP POST method is supported');
        */

        //Load phpseclib
        $this->addVendorLibToPHPPath('phpseclib');
        require_once('Crypt' . DS . 'RSA.php');
        $rsa = new Crypt_RSA();

        $keySize = isset($this->request->data['key_size']) ? $this->request->data['key_size'] : 2048;

        $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);

        $keyPair = $rsa->createKey($keySize);

        $publicKey = $keyPair['publickey'];
        $privateKey = $keyPair['privatekey'];

        //Trim phpseclib name from end of public key
        $publicKey = str_replace(' phpseclib-generated-key','',$publicKey);

        echo json_encode(array(
            'privateKey' => $privateKey,
            'publicKey' => $publicKey
        ));
    }
}

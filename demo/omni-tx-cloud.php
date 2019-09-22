<?php
require('../vendor/autoload.php');

use OmniTool\Wallet;

$wallet = Wallet::cloud('./testnet.wallet','testnet');
$addressList = $wallet->getAddressList();
var_dump($addressList);

$from = $addressList[0];
$to = $addressList[1];
$propertyId = 2; //TOMNI
$amount = "0.000001";

$rawtx = $wallet->omniSendTx($from,$to,$propertyId,$amount);
echo 'rawtx => ' . $rawtx . PHP_EOL;

$ret = $wallet->broadcast($rawtx);
var_dump($ret);
<?php
require('../vendor/autoload.php');

use OmniTool\Wallet;

$wallet = Wallet::local('http://user:123456@localhost:18332','./regtest.wallet','regtest');
$wallet->addKey('4aec8e45106e9fd56b2843b53d4a0b70a3c69f59fcf8edd00d5c5af494a4e05b');
$addressList = $wallet->getAddressList();
var_dump($addressList);

$from = $addressList[0];
$to = 'mnRo8JyTHDd5NxRb3UvGbAhCBPQTQ4UZ8W'; //regtest address
$propertyId = 2147483653; //test token
$amount = "20";

$rawtx = $wallet->omniSendTx($from,$to,$propertyId,$amount);
echo 'rawtx => ' . $rawtx . PHP_EOL;

$ret = $wallet->broadcast($rawtx);
var_dump($ret);
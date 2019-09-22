<?php
require('../vendor/autoload.php');

use OmniTool\Wallet;

$wallet = new Wallet('./testnet.wallet','testnet');
$address = $wallet->getNewAddress();
echo 'address => ' . $address . PHP_EOL;
$key = $wallet->getKeyByAddress($address);
echo 'key => ' . $key->getHex() . PHP_EOL;
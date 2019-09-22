<?php
require('../vendor/autoload.php');

use OmniTool\Wallet;

$wallet = Wallet::cloud('./testnet.wallet','testnet');
$addressList = $wallet->getAddressList();
var_dump($addressList);

$to = 'moneyqMan7uh8FqdCA2BV5yZ8qVrc9ikLP'; //omni testnet faucet
$rawtx = $wallet->btcSendTx($addressList[0],$to,1000,500);
echo 'rawtx => ' . $rawtx . PHP_EOL;

$ret = $wallet->broadcast($rawtx);
var_dump($ret);
<?php
require('../vendor/autoload.php');

use OmniTool\RpcClient;

$client = new RpcClient('http://user:123456@localhost:18332');

$ret = $client->btc->getNewAddress();
$issuer = $ret->result;
echo 'issuer address => ' . $issuer . PHP_EOL;

$ret = $client->btc->getNewAddress();
$tommy = $ret->result;
echo 'tommy address => ' . $to . PHP_EOL;

$ret = $client->btc->sendToAddress($issuer,1.5);
echo 'fund issuer tx hash => ' . $ret->result . PHP_EOL;

$ret = $client->btc->generate(6);
echo 'fund issuer tx mined ' . PHP_EOL;

$ret = $client->btc->listUnspent(1,999999,[$issuer]);
$balance = 0;
foreach($ret->result as $utxo){
  $balance += $utxo->amount;
}
echo 'issuer bitcoin balance now => ' . $balance . PHP_EOL;

$name = 'TOKEN_' . time();
$ret = $client->omni->sendIssuanceFixed($issuer,2,2,0,'','',$name,'','','10000');
echo 'issue 10000 test token tx hash => ' . $ret->result . PHP_EOL;

$ret = $client->btc->generate(6);
echo 'token issuance tx mined ' . PHP_EOL;

$ret = $client->omni->getAllBalancesForAddress($issuer);
$propertyId = $ret->result[0]->propertyid;
$balance = $ret->result[0]->balance;
echo 'token id => ' . $propertyId . PHP_EOL;
echo 'issuer omni token balance => ' . $balance . PHP_EOL;

$ret = $client->omni->send($issuer,$tommy,$propertyId,'100.25');
echo 'transfer tx hash => ' . $ret->result . PHP_EOL;

$ret = $client->btc->generate(6);
echo 'token transfer tx mined ' . PHP_EOL;

$ret = $client->omni->getAllBalancesForAddress($tommy);
foreach($ret->result as $item){
  echo 'tommy balance => ' . $item-> balance . ' for ' . $item->propertyid . PHP_EOL;
}

$ret = $client->btc->listUnspent(1,999999,[$tommy]);
$balance = 0;
foreach($ret->result as $utxo){
  $balance += $utxo->amount;
}
echo 'tommy bitcoin balance => ' . $balance . PHP_EOL;
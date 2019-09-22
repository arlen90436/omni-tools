<?php
require('../vendor/autoload.php');

use OmniTool\LocalExplorer;

$explorer = new LocalExplorer('http://user:123456@localhost:18332');

$address = 'mjMxzdE1XUgDdVxPXNqjcVrSWQjVcp1s2v';

$balance = $explorer->getBtcBalance($address);
echo 'btc balance => ' . $balance . PHP_EOL;

$balance = $explorer->getOmniBalance($address);
echo 'omni balance => ' . PHP_EOL;
var_dump($balance);

$balance = $explorer->getOmniBalance($address,2147483653);
echo 'test token balance => ' . $balance['balance'] . PHP_EOL;
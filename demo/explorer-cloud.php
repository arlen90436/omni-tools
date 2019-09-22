<?php
require('../vendor/autoload.php');

use OmniTool\CloudExplorer;

$explorer = new CloudExplorer('mainnet');

$address = '1Jekm8ZswQmDhLFMp9cuYb1Kcq26riFp6m';

$balance = $explorer->getBtcBalance($address);
echo 'btc balance => ' . PHP_EOL;
var_dump($balance);

$balance = $explorer->getOmniBalance($address);
echo 'omni balance => ' . PHP_EOL;
var_dump($balance);

$balance = $explorer->getOmniBalance($address,31);
echo 'usdt balance => ' . PHP_EOL;
var_dump($balance);


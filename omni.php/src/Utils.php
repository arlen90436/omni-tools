<?php
namespace OmniTool;

use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Address\AddressCreator;

class Utils{
  static function privateKeyFromHex($prvHex) {
    $factory = new PrivateKeyFactory();
    return $factory->fromHexCompressed($prvHex);
  }
  
  static function privateKeyFromWif($wif,$chainId){
    $factory = new PrivateKeyFactory();
    $network = self::chainIdToNetwork($chainId);
    return $factory->fromWif($wif,$network);
  }

  static function p2pkhScriptHexFromPrivateKey($prv){
    return p2pkhScriptFromPrivateKey($prv)->getHex();
  }
  
  static function p2pkhScriptFromPrivateKey($prv){
    return ScriptFactory::scriptPubKey()->p2pkh($prv->getPubKeyHash());
  }

  static function chainIdToNetwork($chainId){
    if($chainId == 'mainnet') return NetworkFactory::bitcoin();
    else if($chainId == 'testnet') return NetworkFactory::bitcoinTestnet();
    else if($chainId == 'regtest') return NetworkFactory::bitcoinRegtest();
    else throw new \Exception('supplied chain id not supported');
  }
  
  static function guessNetworkFromAddress($addrHex){
    $prefix = substr($addrHex,0,1);
    if($prefix == '1' || $prefix == '3') return NetworkFactory::bitcoin();
    if($prefix == '2' || $prefix == 'm' || $prefix == 'n') return NetworkFactory::bitcoinTestnet();
    throw new \Exception('unrecognized address');
  }

  static function scriptHexFromAddress($addrHex){
    return self::scriptFromAddress($addrHex)->getHex();
  }
  
  static function scriptFromAddress($addrHex){
    $ac = new AddressCreator();
    $network = self::guessNetworkFromAddress($addrHex);
    $addr = $ac->fromString($addrHex,$network);
    return $addr->getScriptPubKey();
  }
  
  static function scriptFromHex($scriptHex){
    return ScriptFactory::fromHex($scriptHex);
  }
  
  static function btcToSat($value){
    return intval(floatval($value) * 100000000);
  }
  
  static function satToBtc($value){
    return $value / 100000000;
  }
  
}
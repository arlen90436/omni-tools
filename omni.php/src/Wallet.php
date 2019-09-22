<?php

namespace OmniTool;

use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Transaction\TransactionFactory;
use BitWasp\Bitcoin\Transaction\Factory\Signer;
use BitWasp\Bitcoin\Transaction\TransactionOutput;
use BitWasp\Bitcoin\Serializer\Transaction\TransactionSerializer;
use BitWasp\Buffertools\Buffer;

class Wallet {
  protected $network;
  protected $meta = [
    'chainId'=>'mainnet',
    'keys'=>[]
  ];
  protected $scriptToKey = [];
  protected $addressToKey = [];
  protected $keyDict = [];
  
  protected $utxoCollector;
  protected $utxoSelector;
  protected $broadcaster;
  protected $resolver;
  
  protected $walletFile;
  
  function __construct($walletFile,$chainId="mainnet",$collector=null,$selector=null,$broadcaster=null,$resolver=null){
    $this->network = Utils::chainIdToNetwork($chainId);
    $this->meta['chainId'] = $chainId;
    $this->utxoCollector = $collector;
    $this->utxoSelector = $selector;
    $this->broadcaster = $broadcaster;
    $this->resolver = $resolver;
    
    $this->walletFile = $walletFile;
    $this->load($walletFile);
  }
  
  static function cloud($walletFile,$chainId="mainnet"){
    $collector = new CloudUtxoCollectorSoChain($chainId);
    $selector = new DefaultUtxoSelector();
    $broadcaster = new CloudBroadcaster($chainId);
    $resolver = new CloudPropertyMetaResolver();
    return new self($walletFile,$chainId,$collector,$selector,$broadcaster,$resolver);
  }
  
  static function local($uri,$walletFile,$chainId="regtest"){
    $collector = new LocalUtxoCollector($uri);
    $selector = new DefaultUtxoSelector();
    $broadcaster = new LocalBroadcaster($uri);
    $resolver = new LocalPropertyMetaResolver($uri);
    return new self($walletFile,$chainId,$collector,$selector,$broadcaster,$resolver);
  }
  
  function setUtxoCollector($collector){
    $this->utxoCollector = $collector;
  }
  
  function setUtxoSelector($selector){
    $this->utxoSelector = $selector;
  }
  
  function setBroadcaster($broadcaster){
    $this->broadcaster = $broadcaster;
  }
  
  function setPropertyMetaResolver($resolver){
    $this->resolver = $resolver;
  }
  
  function btcSendTx($fromAddr,$toAddr,$amount,$fee=10000,$changeAddr=null){    
    if($fromAddr==null) $addressList = $this->getAddressList();
    else $addressList = [$fromAddr];
    
    $utxoBag = $this->findUtxo($amount+$fee,$addressList);
    
    if($utxoBag->getCount() == 0 ) throw new \Exception('no utxo at all');    
    
    $changeAmount = $utxoBag->getTotal() - ($amount + $fee);
    
    if( $changeAmount < 0 )  throw new \Exception('no enough utxo');       
        
    $txb = TransactionFactory::build();    
    foreach($utxoBag as $utxo){
  		$txb->input($utxo->txid,$utxo->vout);
    }    
    $txb->output($amount,Utils::scriptFromAddress($toAddr));

    if($changeAmount > 0) {
      if($changeAddr == null) $changeAddr = $this->getChangeAddress();
      $txb->output($changeAmount,Utils::scriptFromAddress($changeAddr));
    }    
  	$tx = $txb->get();        
    
    $signer = new Signer($tx);
    foreach($utxoBag as $idx => $utxo){
      $prv = $this->getKeyByScript($utxo->script);
      $lock = new TransactionOutput(0,Utils::scriptFromHex($utxo->script));
      $signer->sign($idx,$prv,$lock);
    }          
    $signed = $signer->get();
    
    $serializer = new TransactionSerializer();    
    $raw = $serializer->serialize($signed);

    return $raw->getHex();          
  }

  function omniSendTx($fromAddr,$toAddr,$propertyId,$amount,$fundAddr=null,$referenceAmount=546,$fee=10000){    
    $addressList = [$fromAddr];
    if($fundAddr != null) $addressList[] = $fundAddr;
    
    $omniAmount = $this->adjustDivisible($propertyId,$amount);
    $amount = 0;
    
    $utxoBag = $this->findUtxo($amount + $referenceAmount + $fee,$addressList);
    
    if($utxoBag->getCount() == 0 ) throw new \Exception('no utxo at all');
    
    $utxoBag->setFirst($fromAddr);
    if(!$utxoBag->isFirst($fromAddr)) throw new \Exception('from address has no utxo');
    
    $changeAmount = $utxoBag->getTotal() - ($amount + $referenceAmount + $fee);
    
    if( $changeAmount < 0 )  throw new \Exception('no enough utxo');       
        
    $payload = PayloadFactory::create()->simpleSend(0,0,$propertyId,$omniAmount);
    $omniScript = ScriptFactory::create()
                ->op('OP_RETURN')
                ->data(Buffer::hex($payload))
                ->getScript();
    $txb = TransactionFactory::build();
    foreach($utxoBag as $utxo){
  		$txb->input($utxo->txid,$utxo->vout);
    }    

    $changeAddr = $fromAddr;
    if($changeAmount > 0) {
      if($fundAddr != null) $changeAddr = $fundAddr;
      $txb->output($changeAmount,Utils::scriptFromAddress($changeAddr));
    }    
    $txb->output(0,$omniScript);
    $txb->output($referenceAmount,Utils::scriptFromAddress($toAddr));
    
  	$tx = $txb->get();        
    
    $signer = new Signer($tx);
    foreach($utxoBag as $idx => $utxo){
      $prv = $this->getKeyByScript($utxo->script);
      $lock = new TransactionOutput(0,Utils::scriptFromHex($utxo->script));
      $signer->sign($idx,$prv,$lock);
    }          
    $signed = $signer->get();
    
    $serializer = new TransactionSerializer();    
    $raw = $serializer->serialize($signed);

    return $raw->getHex();          
  }
  
  function findUtxo($target,$addressList){
    if(!isset($this->utxoCollector)){
      throw new \Exception('utxo collector not set!');
    }
    if(!isset($this->utxoSelector)){
      throw new \Exception('utxo selector not set!');
    }
    $candidate = $this->utxoCollector->collect($addressList);
    $candidate->setFirst($addressList[0]);    
    $selected = $this->utxoSelector->select($target,$candidate);
    return $selected;
  }
  
  function adjustDivisible($propertyId,$amount){
    if(!isset($this->resolver)){
      throw new \Exception('property meta resolver not set!');
    }
    $meta = $this->resolver->resolve($propertyId);
    if($meta->divisible) {
      return intval(floatval($amount) * 100000000);
    }else {
      return intval($amount);
    }
  }
  
  function broadcast($rawtx){
    if(!isset($this->broadcaster)){
      throw new \Exception('broadcaster not set!');
    }
    return $this->broadcaster->broadcast($rawtx);    
  }
  
  function getNewAddress(){
    $random = new Random();
    $factory = new PrivateKeyFactory();
    $prv = $factory->generateCompressed($random);
    $p2pkh = new PayToPubKeyHashAddress($prv->getPubKeyHash());    
    $address = $p2pkh->getAddress($this->network);
    $this->addKey($prv->getHex());
    return $address;
  }
  
  function addWif($wif,$chainId='mainnet'){
    $factory = new PrivateKeyFactory();
    $network = Utils::chainIdToNetwork($chainId);
    $prv = $factory->fromWif($wif,$network);
    $this->addKey($prv->getHex());  
  }
  
  function addKey($prvHex){
    if(array_key_exists($prvHex,$this->keyDict)) return;
    
    $factory = new PrivateKeyFactory();
    $prv = $factory->fromHexCompressed($prvHex);
    
    $p2pkh = ScriptFactory::scriptPubKey()->p2pkh($prv->getPubKeyHash());
    $this->scriptToKey[$p2pkh->getHex()] = $prv;
    
    $addr =  new PayToPubKeyHashAddress($prv->getPubKeyHash());
    $addrHex = $addr->getAddress($this->network);
    $this->addressToKey[$addrHex] = $prv;
    
    $this->keyDict[$prvHex] = [
      'address' => $addrHex,
      'script' => $p2pkh->getHex()
    ];
    
    $this->meta['keys'][] = $prvHex;
    
    $this->save($this->walletFile);
  }
  
  function getKeys(){
    return $this->meta['keys'];
  }
  
  function getChangeAddress(){
    $list = $this->getAddressList();
    if(count($list) == 0) throw new Exception('empty address list');
    return $list[0];
  }
  
  function getAddressList(){
    $ret = [];
    foreach($this->addressToKey as $addr=>$_){
      $ret[] = $addr;
    }
    return $ret;
  }
    
  function getKeyByAddress($address){
    return $this->addressToKey[$address];
  }
  
  function getScriptList(){
    $ret = [];
    foreach($this->scriptToKey as $script=>$_){
      $ret[] = $script;
    }
    return $ret;
  }
  
  function getKeyByScript($script){
    return $this->scriptToKey[$script];
  }
   
  function getKeyAddress($key){
    if(!array_key_exists($key,$this->keyDict)) return '';
    return $this->keyDict[$key]['address'];
  } 

  function getKeyScript($key){
    if(!array_key_exists($key,$this->keyDict)) return '';
    return $this->keyDict[$key]['script'];
  } 
  
  function getMeta(){
    return $this->meta;
  }
  
  function save($fn){
    $txt = json_encode($this->meta);
    file_put_contents($fn,$txt);
  }
  
  function load($fn){
    if(!file_exists($fn)) return;
    $txt = file_get_contents($fn);
    $meta = json_decode($txt);
    foreach($meta->keys as $key) {
      $this->addKey($key);
    }
  }
  
  /*
  static function load($fn){
    $txt = file_get_contents($fn);
    $json = json_decode($txt);
    $wallet = new self($json->chainId);
    foreach($json->keys as $key) {
      $wallet->addKey($key);
    }
    return $wallet;
  }
  */
}
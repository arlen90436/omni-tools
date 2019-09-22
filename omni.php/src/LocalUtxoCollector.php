<?php
namespace OmniTool;

class LocalUtxoCollector implements UtxoCollectorInterface {
  protected $client;
  
  public function __construct($uri){
    $this->client = new RpcClient($uri);
  }
  
  public function collect($addressList){
    $ret = $this->client->invoke('listunspent',1,999999,$addressList);
    $bag = new UtxoBag();
    foreach($ret->result as $item){
      $value = intval($item->amount*100000000);
      $utxo = new Utxo($item->txid,$item->vout,$value,$item->scriptPubKey,$item->confirmations);
      $bag->add($utxo);
    }
    return $bag;
  }
}
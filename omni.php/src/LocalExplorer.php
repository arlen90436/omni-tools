<?php
namespace OmniTool;

class LocalExplorer implements ExplorerInterface{
  protected $client;
  
  public function __construct($uri){
    $this->client = new RpcClient($uri);
  }
  
  public function getBtcBalance($address){
    $ret = $this->client->btc->listunspent(1,99999,[$address]);
    $balance = 0;
    forEach($ret->result as $utxo){
      $balance += $utxo->amount;
    }
    return $balance;
  }
  
  public function getOmniBalance($address,$propertyId=null){
    if(isset($propertyId)){
      $ret = $this->client->omni->getBalance($address,$propertyId);
      return [
        'id' => $propertyId,
        'balance' => $ret->result->balance
      ];
    }else{
      $ret = $this->client->omni->getAllBalancesForAddress($address);
      return array_map(function($item){
        return [
          'id' => $item->propertyid,
          'balance' => $item->balance
        ];
      },$ret->result);
    }
  }
}
<?php
namespace OmniTool;

use GuzzleHttp\Client;

class CloudExplorer implements ExplorerInterface{
  
  public function __construct($chainId='mainnet'){
    if($chainId != 'mainnet') throw new \Exception("only mainnet supported");
  }
  
  public function getBtcBalance($address){
    $client = new Client();
    $uri = 'https://chain.so/api/v2/get_address_balance/BTC/' . $address;
    $rsp = $client->get($uri);
    $json = json_decode($rsp->getBody());
    return floatval($json->data->confirmed_balance);
  }
  
  public function getOmniBalance($address,$propertyId=null){
    $client = new Client();
    $uri = 'https://api.omniexplorer.info/v1/address/addr/';
    $opts = [
      'form_params' =>[
        'addr' => $address
      ] 
    ];
    $rsp = $client->post($uri,$opts);
    $json = json_decode($rsp->getBody());
    $dict = [];
    foreach($json->balance as $item){
      $value = intval($item->value);
      if($item->divisible) $value = $value / 100000000;
      $dict[$item->id] = [
        'id' => intval($item->id),
        'balance' => $value,
      ];
    }
    if(!isset($propertyId)) return array_values($dict);
    else return $dict[strval($propertyId)];
  }
}
<?php
namespace OmniTool;

use GuzzleHttp\Client;

class CloudBroadcaster implements BroadcasterInterface {
  protected $client;
  protected $chainId;
  
  function __construct($chainId='mainnet'){
    if($chainId != 'mainnet' && $chainId != 'testnet')
      throw new Exception('only mainnet or testnet supported');
      
    $this->client = new Client();
    $this->chainId = $chainId;
  }


  function broadcast($rawtx){
    $url = 'https://chain.api.btc.com/v3/tools/tx-publish';
    if($this->chainId == 'testnet')
      $url = 'https://tchain.api.btc.com/v3/tools/tx-publish';
    $opts = [
      'json' => [
        'rawhex' => $rawtx
      ]
    ];
    $rsp = $this->client->post($url,$opts);
    return json_decode($rsp->getBody());
  }
}
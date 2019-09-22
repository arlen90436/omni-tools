<?php
namespace OmniTool;

class LocalBroadcaster implements BroadcasterInterface {
  protected $client;
  
  function __construct($uri){
    $this->client = new RpcClient($uri);
  }

  function broadcast($rawtx){
    $ret = $this->client->invoke('sendrawtransaction',$rawtx);
    return $ret;
  }
}
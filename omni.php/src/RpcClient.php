<?php
namespace OmniTool;

use GuzzleHttp\Client;

class RpcClient{
  protected $client;
  
  public $btc;
  public $omni;

  public function __construct($uri){
    $opts = [
      'base_uri' => $uri
    ];
    $this->client = new Client($opts);
    $this->btc = new RpcModule($this);
    $this->omni = new RpcModule($this,'omni_');
  }
    
    
  public function version(){
    return '0.1';
  }
  
  public function invoke(){
    $args = func_get_args();
    $method = array_shift($args);
    $opts = [
      'json' => [
        'jsonrpc' => '1.0',
        'id' => time(),
        'method' => $method,
        'params' => $args
      ]
    ];
    $rsp = $this->client->post('/',$opts);
    $json = json_decode($rsp->getBody());
    return $json;
  }
}
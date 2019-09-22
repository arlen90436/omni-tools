<?php
namespace OmniTool;

class RpcModule {
  protected $client;
  protected $prefix;
  
  function __construct($client,$prefix=''){
    $this->client = $client;
    $this->prefix = $prefix;
  }
  
  function __call($name,$arguments){
    $method = strtolower($this->prefix . $name);
    
    return $this->client->invoke($method,...$arguments);
  }
}
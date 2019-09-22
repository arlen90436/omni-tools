<?php
namespace OmniTool;

class LocalPropertyMetaResolver extends PropertyMetaResolverBase implements PropertyMetaResolverInterface{
  protected $client;
  
  public function __construct($uri){
    $this->client = new RpcClient($uri);
  }
  
  public function resolve($id){    
    //echo 'fetch...' .PHP_EOL;
    $ret = $this->client->invoke('omni_getproperty',$id);  
    $meta = $ret->result;
    
    return $meta;
  }
}
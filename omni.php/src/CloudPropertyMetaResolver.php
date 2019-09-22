<?php
namespace OmniTool;

use GuzzleHttp\Client;

class CloudPropertyMetaResolver extends PropertyMetaResolverBase implements PropertyMetaResolverInterface{
  protected $client;
  
  public function __construct(){
    $opts = [
      'base_uri' => 'https://api.omniexplorer.info/v1/property/'
    ];
    $this->client = new Client($opts);
  }
  
  public function resolve($id){
    $meta = $this->resolveLocal($id);
    if($meta) return $meta;
    
    //echo 'fetch...' .PHP_EOL;
    
    $uri = strval($id);
    $rsp = $this->client->get($uri);
    $meta = json_decode($rsp->getBody());
    
    $this->saveLocal($id,$meta);
    return $meta;
  }
}
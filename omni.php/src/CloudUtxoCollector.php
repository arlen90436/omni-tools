<?php
namespace OmniTool;

use GuzzleHttp\Client;

class CloudUtxoCollector implements UtxoCollectorInterface{
//class CloudUtxoCollector{
  protected $client;
  protected $chainId;
  
  function __construct($chainId='mainnet'){
    if($chainId != 'mainnet' && $chainId != 'testnet')
      throw new Exception('only mainnet or testnet supported');
      
    $this->client = new Client();
    $this->chainId = $chainId;
  }

  /*
    https://blockchain.info/unspent?active=3P81wfH1Muvr5gLLTJ3ewUyRQoQXHpiFkM
    {
        
        "unspent_outputs":[
        
            {
                "tx_hash":"8031f4bb1e1eb62ced110c55b89e0a8602269beadc3c1b77eec1e24c214bddbc",
                "tx_hash_big_endian":"bcdd4b214ce2c1ee771b3cdcea9b2602860a9eb8550c11ed2cb61e1ebbf43180",
                "tx_index":376442689,
                "tx_output_n": 0,
                "script":"a914eb150e7c0b216814a97bc9624fd18ca45dcf80b887",
                "value": 10000,
                "value_hex": "2710",
                "confirmations":3705
            },
          
            {
                "tx_hash":"26bbde317d643e28635bab86954b0d31442c4141399cf100b7adea3bbdf83cae",
                "tx_hash_big_endian":"ae3cf8bd3beaadb700f19c3941412c44310d4b9586ab5b63283e647d31debb26",
                "tx_index":376442680,
                "tx_output_n": 0,
                "script":"a914eb150e7c0b216814a97bc9624fd18ca45dcf80b887",
                "value": 10000,
                "value_hex": "2710",
                "confirmations":3705
            },
        }
    }       
  */  
  function collect($addressList){
    $list = join('|',$addressList);
    
    $url = 'https://blockchain.info/unspent?active=' . $list;
    if($this->chainId == 'testnet') 
      $url = 'https://testnet.blockchain.info/unspent?active=' . $list;
    
    //$url = $url . '&confirmations=6';
    
    $rsp = $this->client->get($url);
    $json = json_decode($rsp->getBody());
    //var_dump($json);
    
    $bag = new UtxoBag();
    foreach($json->unspent_outputs as $item) {
      $utxo = new Utxo($item->tx_hash_big_endian,$item->tx_output_n,$item->value,$item->script,$item->confirmations);
      $bag->add($utxo);
    }
    return $bag;
    
  }  
}
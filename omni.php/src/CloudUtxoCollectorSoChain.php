<?php
namespace OmniTool;

use GuzzleHttp\Client;

class CloudUtxoCollectorSoChain implements UtxoCollectorInterface{
//class CloudUtxoCollector{
  protected $client;
  protected $chainId;
  
  function __construct($chainId='mainnet'){
    if($chainId != 'mainnet' && $chainId != 'testnet')
      throw new Exception('only mainnet or testnet supported');
      
    $this->client = new Client();
    $this->chainId = $chainId;
  }

  function collect($addressList){
    $bag = new UtxoBag();
    foreach($addressList as $address) $this->collectSingle($address,$bag);
    return $bag;
  }
  
  private function collectSingle($address,$bag){    
    $url = 'https://chain.so/api/v2/get_tx_unspent/BTC/' . $address;
    if($this->chainId == 'testnet') 
      $url = 'https://chain.so/api/v2/get_tx_unspent/BTCTEST/' . $address;
        
    $rsp = $this->client->get($url);
    $json = json_decode($rsp->getBody());
    
    foreach($json->data->txs as $item) {
      $value = Utils::btcToSat($item->value);
      $utxo = new Utxo($item->txid,$item->output_no,$value,$item->script_hex,$item->confirmations);
      $bag->add($utxo);
    }

  }  
}
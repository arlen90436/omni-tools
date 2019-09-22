<?php
namespace OmniTool;

class DefaultUtxoSelector implements UtxoSelectorInterface {
  protected $spendableScripts = [];
  
  function __construct(){
  }
  
  function select($target,$utxoBag){
    $selectedBag = new UtxoBag();    
    foreach($utxoBag as $utxo){
      if($target >0 && $selectedBag->getTotal() >= $target) break;
      if($this->spendable($utxo)) $selectedBag->add($utxo);
    }
    //if($selectedBag->getTotal() < $target) return null;
    return $selectedBag;
  }
  
  function spendable($utxo){
    if($utxo->confirms < 6) return false;
    return true;
  }
}
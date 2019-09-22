<?php
namespace OmniTool;

class Utxo {
  public $txid;
  public $vout;
  public $value;
  public $script;
  public $confirms;
  
  function __construct($txid,$vout,$value,$script,$confirms){
    $this->txid = $txid;
    $this->vout = $vout;
    $this->value = $value;
    $this->script = $script;
    $this->confirms = $confirms;
  }
  
  function __toString(){
    return 'utxo{' . join(',',[$this->txid , $this->vout,$this->confirms]) . '}';
  }
  
  
}
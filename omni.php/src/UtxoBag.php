<?php
namespace OmniTool;

class UtxoBag implements \Iterator {
  protected $total=0;
  protected $position = 0;
  protected $bag = [];
  
  function __construct(){}

  function getTotal(){
    return $this->total;
  }
  
  function getCount(){
    return count($this->bag);
  }
  
  function add($utxo){
    $this->bag[] = $utxo;
    $this->total += $utxo->value;
    return $this->getCount();
  }

  
  //implement iterator interface
  function rewind(){
    $this->position = 0;
  }
  
  function current(){
    return $this->bag[$this->position];
  }
  
  function key() {
    return $this->position;
  }
  
  function next() {
    ++$this->position;
  }
  
  function valid(){
    return isset($this->bag[$this->position]);
  }
  
  function get($idx){
    return $this->bag[$idx];
  }
  
  function setFirst($address){
    $script = Utils::scriptHexFromAddress($address);
    if($this->bag[0]->script == $script) return; 
    for($i=0;$i<count($this->bag);$i++){
      $utxo = $this->bag[$i];
      if($utxo->script == $script) {
        $sliced = array_splice($this->bag,$i,1);
        $this->bag = array_merge($sliced,$this->bag);
        return;
      }
    }    
  }
  
  function isFirst($address){
    $script = Utils::scriptHexFromAddress($address);
    if($this->bag[0]->script == $script) return true;
    return false;
  }
  
}
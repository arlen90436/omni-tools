<?php
namespace OmniTool\TxFieldType;

class String255 extends TypeBase {
  public function __construct($v){
    $this->buffer = array_fill(0,255,0);
    for($i=0;$i<strlen($v);$i++){
      $this->buffer[$i] = Ord($v[$i]);
    }
  }
}


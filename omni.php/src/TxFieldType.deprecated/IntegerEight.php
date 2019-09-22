<?php
namespace OmniTool\TxFieldType;

class IntegerEight extends TypeBase{
  public function __construct($v){
    $this->pushUint64($v);
  }
}


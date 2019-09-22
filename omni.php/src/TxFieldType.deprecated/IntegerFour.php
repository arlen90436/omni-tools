<?php
namespace OmniTool\TxFieldType;

class IntegerFour extends TypeBase{
  public function __construct($v){
    $this->pushUint32($v);
  }
}

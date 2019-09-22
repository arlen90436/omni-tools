<?php
namespace OmniTool\TxFieldType;

class IntegerTwo extends TypeBase {
  public function __construct($v){
    $this->pushUint16($v);
  }
}

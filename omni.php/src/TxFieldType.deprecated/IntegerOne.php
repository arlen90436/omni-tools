<?php
namespace OmniTool\TxFieldType;

class IntegerOne extends TypeBase {
  public function __construct($v){
    $this->pushUint8($v);
  }
}


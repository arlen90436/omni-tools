<?php
namespace OmniTool\TxFieldType;

class TransactionVersion extends TypeBase {
  public function __construct($v){
    $this->pushUint16($v);
  }
}


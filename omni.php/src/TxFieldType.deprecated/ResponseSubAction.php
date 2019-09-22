<?php
namespace OmniTool\TxFieldType;

class ResponseSubAction extends TypeBase {
  public function __construct($v){
    $this->pushUint8($v);
  }
}


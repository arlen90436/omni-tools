<?php
namespace OmniTool\TxFieldType;

class CurrencyIdentifier extends TypeBase{
  public function __construct($v){
    $this->pushUint32($v);
  }
}
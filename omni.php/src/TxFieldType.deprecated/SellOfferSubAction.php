<?php
namespace OmniTool\TxFieldType;

class SellOfferSubAction extends TypeBase {
  public function __construct($v){
    $this->pushUint8($v);
  }
}


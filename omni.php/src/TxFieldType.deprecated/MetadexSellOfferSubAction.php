<?php
namespace OmniTool\TxFieldType;

class MetadexSellOfferSubAction extends TypeBase {
  public function __construct($v){
    $this->pushUint8($v);
  }
}


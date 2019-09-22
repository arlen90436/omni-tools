<?php
namespace OmniTool\TxFieldType;

class ListingIdentifier extends TypeBase {
  public function __construct($v){
    $this->pushUint32($v);
  }
}


<?php
namespace OmniTool\TxFieldType;

class UTCDatetime extends TypeBase {
  public function __construct($v){
    $this->pushUint64($v);
  }
}


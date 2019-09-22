<?php
namespace OmniTool\TxFieldType;

class TimePeriodInSeconds extends TypeBase {
  public function __construct($v){
    $this->pushUint32($v);
  }
}


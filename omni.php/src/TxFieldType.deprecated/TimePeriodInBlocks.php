<?php
namespace OmniTool\TxFieldType;

class TimePeriodInBlocks extends TypeBase{
  public function __construct($v){
    $this->pushUint8($v);
  }
}

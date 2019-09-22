<?php
namespace OmniTool\TxFieldType;

class EcoSystem extends TypeBase{
  public function __construct($v){
    $this->pushUint8($v);
  }
}


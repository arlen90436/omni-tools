<?php
namespace OmniTool\TxFieldType;

class PropertyType extends TypeBase {
  public function __construct($v){
    $this->pushUint16($v);
  }
}
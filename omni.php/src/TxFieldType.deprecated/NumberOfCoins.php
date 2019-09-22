<?php
namespace OmniTool\TxFieldType;

class NumberOfCoins extends TypeBase {
  public function __construct($v){
    $this->pushInt64($v);
  }
}

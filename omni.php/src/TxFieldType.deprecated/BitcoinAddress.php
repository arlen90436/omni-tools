<?php
namespace OmniTool\TxFieldType;

class BitcoinAddress extends TypeBase{
  public function __construct($v){
    $this->pushAddress($v);
  }
}


<?php
namespace OmniTool;

use BitWasp\Bitcoin\Transaction\TransactionFactory;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Buffertools\Buffer;

define("DUST",546);

class OmniTxBuilder {
  
  public function __construct(){}
  
  public static function send($from,$to,$propertyId,$amount,$redeemAddress=null,$referenceAmount=DUST){
    $data = [
      '6f6d6e69',  //omni : 4 bytes
      '0000',     //version:2 bytes
      '0000',     //tx type: 2 bytes, simple send 
      str_pad(dechex($propertyId),8,'0',STR_PAD_LEFT), //property id: 4 bytes
      str_pad(dechex($amount),16,'0',STR_PAD_LEFT), //num of coins: 8 bytes
    ];
    $buffer = Buffer::hex(join('',$data));
    //var_dump($buffer);

    $txb = TransactionFactory::build();
    $script = ScriptFactory::create()
                ->op('OP_RETURN')
                ->data($buffer)
                ->getScript();
    var_dump($script);
  }

}
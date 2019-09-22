<?php
namespace OmniTool\TxFieldType;

use BitWasp\Bitcoin\Base58;

class TypeBase implements TypeInterface{
  protected $buffer = [];
  
  public function serialize(){
    $bin = pack('C*',...$this->buffer);
    return bin2hex($bin);
  }
  
  public function pushUint8($v){
    $this->buffer[] = $v & 0xff;
  }
  
  public function pushUint16($v){
    $this->buffer[] = ($v >> 8) & 0xff ;
    $this->buffer[] = $v & 0xff;    
  }
  
  public function pushUint32($v){
    $this->buffer[] = ($v >> 24) & 0xff ;
    $this->buffer[] = ($v >> 16) & 0xff ;
    $this->buffer[] = ($v >> 8) & 0xff ;
    $this->buffer[] = $v & 0xff;    
  }
  
  public function pushUint64($v){
    $this->buffer[] = ($v >> 56) & 0xff ;
    $this->buffer[] = ($v >> 48) & 0xff ;
    $this->buffer[] = ($v >> 40) & 0xff ;
    $this->buffer[] = ($v >> 32) & 0xff ;
    $this->buffer[] = ($v >> 24) & 0xff ;
    $this->buffer[] = ($v >> 16) & 0xff ;
    $this->buffer[] = ($v >> 8) & 0xff ;
    $this->buffer[] = $v & 0xff;    
  }
  
  public function pushInt64($v){
    //todo..
  }
  
  public function pushArray($v){
    foreach($v as $e){
      $this->buffer[] = $e & 0xff;
    }  
  }
  
  public function push(){
    $args = func_get_args();
    $this->pushArray($args);
  }  
  
  public function pushString($v){
    for($i=0;$i<strlen($v);$i++){
      $this->buffer[] = Ord($v[$i]);
    }
  }
  
  
  public function pushString255($v){
    $a = array_fill(0,255,0);
    for($i=0;$i<strlen($v);$i++){
      $a[$i] = Ord($v[$i]);
    }
    $this->pushArray($a);
  }
  
  public function pushAddress($v){
    $decoded = Base58::decode($v);
    $stripped = $decoded->slice(0,21);
    $this->pushString($stripped->getBinary());
  }
}
<?php
namespace OmniTool;

define('PROTOCAL_SPEC_FILE',__DIR__ . DIRECTORY_SEPARATOR .'protocol-spec.json');
define('DUST',546);
define('MAGIC',bin2hex("omni"));

class PayloadFactory{
  protected $spec;
  
  public function __construct(){
    $this->loadProtocol();
  }
  
  public static function create(){
    return new self();
  }
  
  private function loadProtocol(){
    $txt = file_get_contents(PROTOCAL_SPEC_FILE);
    $this->spec = json_decode($txt);
  }
    
  public function __call($name,$arguments){
    //echo 'name => ' . $name . PHP_EOL;
    //echo 'arguments => ' . implode(',',$arguments) . PHP_EOL;
    $tx = $this->spec->transaction->{$name};
    if(!isset($tx)) {
      throw new \Exception("payload not registered");
    }
    //var_dump($tx);
    if(count($arguments) != count($tx)){
      throw new \Exception("field count mismatch with spec");
    }
    $sb = new SerializeBuffer();
    for($i=0;$i<count($arguments);$i++){
      $value = $arguments[$i];
      //echo 'value => ' . $value . PHP_EOL;
      $type = $tx[$i];
      $alias = $this->spec->alias->{$type};
      $method = 'push' . ucfirst($alias);
      //echo 'method => ' . $method . PHP_EOL;
      $sb->{$method}($value);
    }
    return MAGIC . $sb->getHex();
  }
}
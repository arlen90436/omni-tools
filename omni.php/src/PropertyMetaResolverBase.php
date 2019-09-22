<?php
namespace OmniTool;

define('CACHE_PATH',__DIR__ . DIRECTORY_SEPARATOR . 'cache');

class PropertyMetaResolverBase{
  
  protected function resolveLocal($id){
    $metaFile = CACHE_PATH . DIRECTORY_SEPARATOR . strval($id) . '.json';
    if(file_exists($metaFile)) {
      $txt = file_get_contents($metaFile);
      return json_decode($txt);
    }
  }
  
  protected function saveLocal($id,$meta){
    $metaFile = CACHE_PATH . DIRECTORY_SEPARATOR . strval($id) . '.json';
    $txt = json_encode($meta);
    file_put_contents($metaFile,$txt);  
  }
}
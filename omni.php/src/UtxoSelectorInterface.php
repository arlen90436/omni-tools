<?php
namespace OmniTool;

interface UtxoSelectorInterface{
  public function select($target,$utxoBag);
}
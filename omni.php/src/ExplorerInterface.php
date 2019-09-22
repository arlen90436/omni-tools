<?php
namespace OmniTool;

interface ExplorerInterface{
  public function getBtcBalance($address);
  public function getOmniBalance($address,$propertyId);
}
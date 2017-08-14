<?php
class Scriptlodge_Datapass_Model_Mysql4_Datapass extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("datapass/datapass", "entity_id");
    }
}
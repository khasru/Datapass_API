<?php
class Scriptlodge_Datapass_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
        $cid = $this->getRequest()->getParam('cid');        
        $customer = Mage::getModel('customer/customer')->load($cid);        
        if ($customer) {
            Mage::getModel('datapass/datapass')->proccessForCustomerReg($customer);
        }
        //  print_r($customer->getData());
        exit('test');
    }
}
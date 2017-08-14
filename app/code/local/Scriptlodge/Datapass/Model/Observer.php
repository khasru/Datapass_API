<?php

class Scriptlodge_Datapass_Model_Observer {

    public function getDatapasskey($observer) {
        $controller = $observer->getEvent()->getData('controller_action');
        if ('customer_account_login' == $controller->getFullActionName()) {
            $refdatapass = Mage::app()->getRequest()->getParam('refdatapass');
            if (isset($refdatapass)) {
                $refdatapass = Mage::helper('core')->urlDecode(Mage::app()->getRequest()->getParam('refdatapass'));
                Mage::getSingleton('core/session')->setDatapassRef($refdatapass);
                //echo $myValue = Mage::getSingleton('core/session')->getDatapassRef();
            }
        }
    }

    public function sendDatapassRequest($observer) {
        
        $datapassRef = Mage::getSingleton('core/session')->getDatapassRef();
        if ($datapassRef) {
            $customer = $observer->getEvent()->getCustomer();
            //print_r($customer->getData());
            $custMobile = $customer->getCustMobile();
            if ($customer->getCustMobile() || $customer->getMobile()) {
                Mage::getModel('datapass/datapass')->proccessForCustomerReg($customer);
                Mage::getSingleton('core/session')->setDatapassRef("");
            }
        }
    }

}

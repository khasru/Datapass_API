<?php

class Scriptlodge_Datapass_Model_Datapass extends Mage_Core_Model_Abstract {

    const AUTH_URL = 'https://datapass.co/datapass/v2/service/enterprise/auth';
    const ACTION_URL = 'https://datapass.co/datapass/v2/service/enterprise/action';

    protected function _construct() {

        $this->_init("datapass/datapass");
    }

    public function proccessForCustomerReg($customer = "") {
        //print_r($customer->getData());
        $requestData = array();
        $msisdn = "";
        $active = (int) Mage::getStoreConfig(Scriptlodge_Datapass_Helper_Data::XML_PATH_CUSTOMER_REG_ACTIVE);
        if ($active == 0)
            return false;

        if (!empty($customer->getCustMobile())) {
            $msisdn = $customer->getCustMobile();
        } elseif (!empty($customer->getMobile())) {
            $msisdn = $customer->getMobile();
        }
        //echo $msisdn;
        if (empty($msisdn))
            return false;
        $msisdn = substr($msisdn, -11);
        
        $actionCode = 'Purchase';

        $requestData['username'] = (string) Mage::getStoreConfig(Scriptlodge_Datapass_Helper_Data::XML_PATH_CUSTOMER_REG_USERNAME);
        $requestData['password'] = (string) Mage::getStoreConfig(Scriptlodge_Datapass_Helper_Data::XML_PATH_CUSTOMER_REG_PASSWORD);
        $requestData['enterpriseUuid'] = (string) Mage::getStoreConfig(Scriptlodge_Datapass_Helper_Data::XML_PATH_CUSTOMER_REG_ENTERPRISE_UUID);
        $requestData['campaignUuid'] = (string) Mage::getStoreConfig(Scriptlodge_Datapass_Helper_Data::XML_PATH_CUSTOMER_REG_CAMPAIGN_UUID);
        $requestData['msisdn'] = $msisdn;
        $requestData['actionCode'] = $actionCode;
        $_datapass = $requestData;
        $_datapass['customer_id'] = $customer->getId();
        $_datapass['order_id'] = "";
        $_datapass['campaign_type'] = "Registration";
        $_id = $this->saveInfo($_datapass);

        $_requestData = json_encode($requestData);
        $token = $this->sendAuthenticationRequestToDatapass($_requestData);
//        echo $token;
//        exit();
        if ($token) {
            $requestActionData['enterpriseUuid'] = $requestData['enterpriseUuid'];
            $requestActionData['actionCode'] = $requestData['actionCode'];
            $requestActionData['campaignUuid'] = $requestData['campaignUuid'];
            $requestActionData['msisdn'] = $requestData['msisdn'];
            $requestActionData['token'] = $token;
            $_requestActionData = json_encode($requestActionData);
            $_result = $this->sendDataRequestToDatapass($_requestActionData);
            if ($_result) {
                $_datapass = Mage::getModel('datapass/datapass')->load($_id);
                $_datapass->setData('status', 1);
                $_datapass->save();
                return true;
            }
        }
    }

    public function sendAuthenticationRequestToDatapass($data = "") {
      //  print_r($data);
        $url = self::AUTH_URL;
        $headers = array('Content-Type:application/json');

        $post = curl_init($url);
        // curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_HEADER, 0);
        curl_setopt($post, CURLOPT_POST, 1);
        curl_setopt($post, CURLOPT_TIMEOUT, 60);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($post, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($post, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($post, CURLOPT_POSTFIELDS, $data);
        curl_setopt($post, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($post);
        //echo "<pre>";
      //  print_r($result);
        //$info = curl_getinfo($post);                
        $http_response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
        //  $http_response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);

        curl_close($post);
        if (200 === $http_response_code) {
            $resultArray = json_decode($result);
            if ($resultArray->body->token) {
                return $resultArray->body->token;
            } else {
                return false;
            }
        } else {
            //die('Error: "' . curl_error($post) . '" - Code: ' . curl_errno($post));
            //$errors = curl_error($post);        
            return false;
        }
    }

    public function sendDataRequestToDatapass($data = "") {

        $url = self::ACTION_URL;
        $headers = array('Content-Type:application/json');

        $post = curl_init($url);
        // curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_HEADER, 0);
        curl_setopt($post, CURLOPT_POST, 1);
        curl_setopt($post, CURLOPT_TIMEOUT, 60);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($post, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($post, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($post, CURLOPT_POSTFIELDS, $data);
        curl_setopt($post, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($post);
//        echo "<pre>";
    //    print_r($result);
        //$info = curl_getinfo($post);                
        $http_response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
        //  $http_response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
        curl_close($post);

        if (200 === $http_response_code) {
            $resultArray = json_decode($result);
            if ($resultArray->body->status) {
                return $resultArray->body->status;
            } else {
                return false;
            }
        } else {
            //die('Error: "' . curl_error($post) . '" - Code: ' . curl_errno($post));
            //$errors = curl_error($post);        
            return false;
        }
    }

    public function saveInfo($_data = "") {
        $datapass = Mage::getModel('datapass/datapass');
        if (isset($_data['customer_id'])) {
            $datapass->setData('customer_id', $_data['customer_id']);
        }
        if (isset($_data['order_id'])) {
            $datapass->setData('order_id', $_data['order_id']);
        }
        if (isset($_data['customer_id'])) {
            $datapass->setData('customer_id', $_data['customer_id']);
        }
        if (isset($_data['msisdn'])) {
            $datapass->setData('cust_mobile', $_data['msisdn']);
        }
        if (isset($_data['campaignUuid'])) {
            $datapass->setData('campaign_id', $_data['campaignUuid']);
        }
        if (isset($_data['campaign_type'])) {
            $datapass->setData('campaign_type', $_data['campaign_type']);
        }
        $createAt = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
        $datapass->setData('created_at', $createAt);
        $datapass->save();
        return $datapass->getId();
    }

}

<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `datapass` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `customer_id` int(11) NOT NULL COMMENT 'customer id',
  `order_id` int(11) NOT NULL COMMENT 'Order id',
  `cust_mobile` text NOT NULL COMMENT 'Customer Mobile',
  `campaign_id` text COMMENT 'campaign id',
  `campaign_type` text COMMENT 'campaign type',
  `status` smallint(6) DEFAULT 0 COMMENT 'Status',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation Time',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='datapass'

		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
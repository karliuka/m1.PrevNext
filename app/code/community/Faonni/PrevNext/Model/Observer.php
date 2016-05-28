<?php
/**
 * Faonni
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future.
 * 
 * @package     Faonni_PrevNext
 * @copyright   Copyright (c) 2016 Karliuka Vitalii(karliuka.vitalii@gmail.com) 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Faonni_PrevNext_Model_Observer
{
    /**
     * Retrieve PrevNext position model
     *
     * @return Faonni_PrevNext_Model_Position
     */
    public function getPosition()
    {
        return Mage::getSingleton('faonni_prevnext/position');
    }
	
    /**
     * Retrieve core session model
     *
     * @return Mage_Core_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('core/session');
    }
	
    /**
     * Init position
     *
     * @param   Varien_Event_Observer $observer
     * @return  Faonni_PrevNext_Model_Observer
     */
    public function init(Varien_Event_Observer $observer)
    {
		$collection = $observer->getEvent()->getCollection();
		
		$position = $this->getPosition();
		$position->init($collection);
		$position->setProductsUrl($collection);

        return $this;
    }
	
    /**
     * Set Cache Id
     *
     * @param   Varien_Event_Observer $observer
     * @return  Faonni_PrevNext_Model_Observer
     */
    public function setCacheId(Varien_Event_Observer $observer)
    {
		$position = $this->getPosition();
		$cacheId = Mage::app()->getRequest()->getParam(
			$position->getIdVarName(), 
			false
		);
		$position->setCacheId($cacheId);
		
        return $this;
    }	
}
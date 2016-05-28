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
class Faonni_PrevNext_Model_Position
	extends Varien_Object
{
    /**
     * GET parameter CacheId variable
     *
     * @var string
     */
    protected $_idVarName = 'c';
	
    /**
     * CacheId variable
     *
     * @var string
     */
    protected $_cacheId;
	
    /**
     * Collection items
     *
     * @var array
     */
    protected $_items = array();
	
    /**
     * Retrieve cache model
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return Mage::app()->getCache();
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
     * Retrieve connection for read data
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function getAdapter()
	{
		return $this->getResource()->getConnection('catalog_read');		
	}
	
    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource
     */
    public function getResource()
    {
        return Mage::getSingleton('core/resource');
    }
	
    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }
	
    /**
     * Getter for $_idVarName
     *
     * @return string
     */
    public function getIdVarName()
    {
        return $this->_idVarName;
    }
	
    /**
     * Set Cache Id
     *
     * @param string $cacheId
     * @return Faonni_PrevNext_Model_Position
     */
    public function setCacheId($cacheId)
    {
        $this->_cacheId = $cacheId;
        return $this;
    }

    /**
     * Retrive Cache Id
     *
     * @return string
     */
    public function getCacheId()
    {
        return $this->_cacheId;
    }

    /**
     * Build Cache Id
     *
     * @param string $select
     * @return Faonni_PrevNext_Model_Position
     */
    public function buildCacheId($select)
    {
        $cacheId = md5(
			md5('position-store-' . $this->getStore()->getId()) . 
			md5($select)
		);
        return $cacheId;
    }
	
    /**
     * Retrieve Product URL
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductUrl($product)
    {
		$params = array();
		$params['_query'] = array($this->getIdVarName() => $this->getCacheId());
        if (!Mage::app()->getUseSessionInUrl()) {
            $params['_nosid'] = true;
        }
        return $product->getUrlModel()->getUrl($product, $params);
    }
	
    /**
     * Set products url
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Faonni_PrevNext_Model_Position
     */
    public function setProductsUrl($collection)
    {
		foreach ($collection as $product) {
			$product->setData('url', $this->getProductUrl($product));
		}
        return $this;
    }
	
    /**
     * Retrive positions products collection
     *
     * @return array
     */
    public function getCollection()
    {
		$this->load($this->getCacheId());
		
		return $this->_items;
    }
	
    /**
     * Initialize collection select
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return string
     */
    protected function getSelect($collection)
    {
		$select = clone $collection->getSelect();
		$select
			->reset(Zend_Db_Select::LIMIT_COUNT)
			->reset(Zend_Db_Select::LIMIT_OFFSET);
		
        return (string) $select;
    }
	
    /**
     * Load collection
     *
     * @param string $cacheId
     * @param string $select	 
     * @return array
     */
    public function load($cacheId, $select=null)
    {
		$items = $this->getCache()->load($cacheId);
		if ($items) {
			$this->_items = unserialize($items);
		} elseif ($select) {
			$this->_items = $this->getAdapter()->fetchCol($select);
			$this->getCache()->save(serialize($this->_items), $cacheId, array('PREV_NEXT_COLLECTION'));
		}
    }
	
    /**
     * Init position collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Faonni_PrevNext_Model_Position
     */
    public function init($collection)
    {
		$select = $this->getSelect($collection);
		$cacheId = $this->buildCacheId($select);

		if (null == $this->getCacheId()) {
			$this->setCacheId($cacheId);
		}
		if (null != $this->getCacheId()) {
			$this->load($cacheId, $select);
		}		
        return $this;
    }	
}
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
class Faonni_PrevNext_Block_Navigation
	extends Mage_Core_Block_Template
{
    /**
     * Prev product Id
     *
     * @var integer
     */
	 protected $_prevProductId;
	 
    /**
     * Next product Id
     *
     * @var integer
     */
	 protected $_nextProductId;
	 
    /**
     * Prev product
     *
     * @var Mage_Catalog_Model_Product
     */
	 protected $_prevProduct;
	 
    /**
     * Next product
     *
     * @var Mage_Catalog_Model_Product
     */
	 protected $_nextProduct;
	 
    /**
     * Prev Next products collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
	 protected $_collection;	
	 
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
     * Retrive current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }
	
    /**
     * Retrieve array of product id's
     *
     * @return array
     */
    public function getProductsPosition()
    {
        return $this->getPosition()->getCollection();
    }
	
    /**
     * Retrieve position of current product
     *
     * @return string
     */
    public function getProductPosition()
    {
		return array_search($this->getProduct()->getId(), $this->getProductsPosition());
    }
	
    /**
     * Retrive prev product Id
     *
     * @return integer
     */
    public function getPrevProductId()
    {
		if(null == $this->_prevProductId){
			$prevProductIds = array_slice(
				$this->getProductsPosition(), 
				0, 
				$this->getProductPosition()
			);
			$this->_prevProductId = (int)array_pop($prevProductIds);				
		}
		return $this->_prevProductId;
    }
	
    /**
     * Retrive next product Id
     *
     * @return integer
     */
    public function getNextProductId()
    {
		if(null == $this->_nextProductId){
			$nextProductIds = array_slice(
				$this->getProductsPosition(), 
				$this->getProductPosition() + 1, 
				count($this->getProductsPosition())
			);
			$this->_nextProductId = (int)array_shift($nextProductIds);				
		}
		return $this->_nextProductId;		
    }
	
    /**
     * Retrive prev next products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getCollection()
    {
		if (null === $this->_collection){
			$this->_collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
				->addAttributeToFilter(
					'entity_id', 
					array('in' => array($this->getPrevProductId(), $this->getNextProductId()))
				);
		}		
		return $this->_collection;	
    }
	
    /**
     * Retrive prev product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getPrevProduct()
    {
		if (null == $this->_prevProduct){
			$this->_prevProduct = $this->getCollection()->getItemById($this->getPrevProductId());
		}
		return $this->_prevProduct;
    }
	
    /**
     * Retrive next product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getNextProduct()
    {
		if (null == $this->_nextProduct){
			$this->_nextProduct = $this->getCollection()->getItemById($this->getNextProductId());
		}
		return $this->_nextProduct;
    }
	
    /**
     * Retrive next product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getNextProductUrl()
    {
		$product = $this->getNextProduct();
		if ($product) {
			return $this->getPosition()->getProductUrl($product);
		}
		return null;
    }
	
    /**
     * Retrive prev product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getPrevProductUrl()
    {
		$product = $this->getPrevProduct();
		if ($product) {
			return $this->getPosition()->getProductUrl($product);
		}
		return null;		
    }
	
    /**
     * Has prev product
     *
     * @return bool
     */
    public function hasPrevProduct()
    {
		return (bool)$this->getPrevProduct();
    }
	
    /**
     * Has prev product url
     *
     * @return bool
     */
    public function hasNextProduct()
    {
		return (bool)$this->getNextProduct();	
    }	
}
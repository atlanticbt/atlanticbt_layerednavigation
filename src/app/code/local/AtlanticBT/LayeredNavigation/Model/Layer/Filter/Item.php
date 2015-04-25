<?php

class AtlanticBT_LayeredNavigation_Model_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $isActive = Mage::helper('atlanticbt_layerednavigation')->isCategoryLinksEnabled();
        if($isActive && $this->getFilter()->getRequestVar() == "cat") {
            $url = Mage::getModel('catalog/category')->load($this->getValue())->getUrl();
            $request = Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
            $query_string = strpos($request,'?') !== false ? substr($request,strpos($request,'?')) : '';
            return $url . $query_string;
        }
        return parent::getUrl();
    }
}
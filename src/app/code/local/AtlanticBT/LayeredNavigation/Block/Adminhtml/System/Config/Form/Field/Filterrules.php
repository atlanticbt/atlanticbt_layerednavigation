<?php
class AtlanticBT_LayeredNavigation_Block_Adminhtml_System_Config_Form_Field_Filterrules
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        /** @var AtlanticBT_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('atlanticbt_layerednavigation');

        $this->addColumn(
            'code',
            array(
                'label' => $helper->__('Code'),
                'class' => 'required-entry'

            )
        );

        $this->addColumn(
            'category_id',
            array(
                'label' => $helper->__('Category Id'),
            )
        );

        $this->_addAfter = true;
        $this->_addButtonLabel = $helper->__('Add Filter Rule');

        parent::__construct();
    }
} 
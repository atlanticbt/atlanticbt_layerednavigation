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
                'label' => $helper->__('Attribute'),
            )
        );

        $this->addColumn(
            'category_id',
            array(
                'label' => $helper->__('Category'),
            )
        );

        $this->_addAfter = true;
        $this->_addButtonLabel = $helper->__('Add Filter Rule');

        parent::__construct();
        $this->setTemplate('atlanticbt/config/form/field/array.phtml');
    }

    protected function _renderCellTemplate($columnName)
    {
        if($columnName == "code")
            return $this->_renderAttributesDropdown();
        if($columnName == "category_id")
            return $this->_renderCategoriesDropdown();
        return parent::_renderCellTemplate($columnName);
    }

    protected function _renderAttributesDropdown()
    {
        $column = $this->_columns["code"];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . "code" . ']';

        $class =                     'class="' .($column['class']?:"").' select" ';
        $style = $column['style'] ?  'style="' . $column['style'] . '" ' : '';
        $size  = $column['size']  ?  'size="'  . $column['size']  . '" ' : '';

        /* @var $attributes Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->setOrder('is_filterable','desc')->getItems();

        $rendered = '<select name="'.$inputName.'" '.$class.$style.$size.'>';
        $rendered .= '<option value=""> * Select a Filterable Attribute * </option>';
        foreach ($attributes as $attr) {
            $attrClass = $attr->getIsFilterable() ? 'class="active" ' : 'class="inactive" ';
            $attrStyle = $attr->getIsFilterable() ? '' : 'style="display:none;" ';
            $attrLabel = addslashes($attr->getFrontendLabel());
            if(!empty($attrLabel))
                $rendered .= '<option value="'.$attr->getAttributecode().'"'.$attrClass.$attrStyle.'>'.$attrLabel.'</option>';
        }
        $rendered .= '</select>';

        return $rendered;
    }

    protected function _renderCategoriesDropdown()
    {
        $column = $this->_columns["category_id"];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . "category_id" . ']';

        $class =                     'class="' .($column['class']?:"").' select" ';
        $style = $column['style'] ?  'style="' . $column['style'] . '" ' : '';
        $size  = $column['size']  ?  'size="'  . $column['size']  . '" ' : '';

        /* @var $categories Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addAttributeToFilter('level', array('gt' => 0));
        $categories->addAttributeToSelect('name')->addAttributeToSelect('level')->addAttributeToSelect('is_active');
        $categories->addAttributeToSort('is_active', 'desc');
        $categories->addAttributeToSort('path', 'asc');
        $categories->load();

        $rendered = '<select name="'.$inputName.'" '.$class.$style.$size.'>';
        $rendered .= '<option value=""> * Select an Active Category * </option>';
        foreach ($categories as $category) {
            $catClass = $category->getIsActive() ? 'class="active" ' : 'class="inactive" ';
            $catStyle = $category->getIsActive() ? '' : 'style="display:none;" ';
            $catLabel = str_repeat('&nbsp;&nbsp;', $category->getLevel()-1) . ' ' . addslashes($category->getName());
            $rendered .= '<option value="'.$category->getId().'"'.$catClass.$catStyle.'>'.$catLabel.'</option>';
        }
        $rendered .= '</select>';

        return $rendered;
    }
}
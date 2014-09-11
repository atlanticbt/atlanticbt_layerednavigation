<?php
class AtlanticBT_LayeredNavigation_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_FILTER_RULES             = 'atlanticbt_layerednavigation/config/filter_rules';
    const CONFIG_PATH_FILTER_RULES_EXCLUSION   = 'atlanticbt_layerednavigation/config/filter_rules_exclusion';
    const CONFIG_PATH_DISPLAY_LIMIT_ACTIVE     = 'atlanticbt_layerednavigation/display_limit/active';
    const CONFIG_PATH_DISPLAY_LIMIT_LIMIT      = 'atlanticbt_layerednavigation/display_limit/limit';
    const CONFIG_PATH_DISPLAY_LIMIT_MORE_TEXT  = 'atlanticbt_layerednavigation/display_limit/more_text';
    const CONFIG_PATH_DISPLAY_LIMIT_LESS_TEXT  = 'atlanticbt_layerednavigation/display_limit/less_text';
    const REGISTRY_KEY_FILTER_RULES            = 'atlanticbt_layerednavigation_filter_rules';
    const REGISTRY_KEY_FILTER_RULES_EXCLUSION  = 'atlanticbt_layerednavigation_filter_rules_exclusion';
    const RULES_TYPE_INCLUSION                 = 'inclusion';
    const RULES_TYPE_EXCLUSION                 = 'exclusion';

    protected $_layeredNavNum = 0;

    /**
     * gets the filter rules
     * @var string $type
     * @return array
     */
    public function getFilterRules($type)
    {
        switch ($type) {
            case self::RULES_TYPE_EXCLUSION:
                $registryKey = self::REGISTRY_KEY_FILTER_RULES_EXCLUSION;
                break;
            case self::RULES_TYPE_INCLUSION:
            default:
                $registryKey = self::REGISTRY_KEY_FILTER_RULES;
                break;
        }
        $filterRules = Mage::registry($registryKey);
        if (is_null($filterRules)) {
            $filterRules = $this->buildFilterRules($type);
            Mage::register($registryKey, $filterRules);
        }
        return $filterRules;
    }

    /**
     * Builds the filter rules from the config settings
     * @var string $type
     * @return array
     */
    public function buildFilterRules($type)
    {
        switch ($type) {
            case self::RULES_TYPE_EXCLUSION:
                $configPath = self::CONFIG_PATH_FILTER_RULES_EXCLUSION;
                break;
            case self::RULES_TYPE_INCLUSION:
            default:
            $configPath = self::CONFIG_PATH_FILTER_RULES;
                break;
        }
        $configRules = Mage::getStoreConfig($configPath);
        if ($configRules) {
            $configRules = unserialize($configRules);
        }

        if (!is_array($configRules)) {
            $configRules = array();
        }

        // now organize the rules for easy searching
        $filterRules = array();
        foreach ($configRules as $rule) {
            $code = $rule['code'];
            if (!array_key_exists($code, $filterRules)) {
                $filterRules[$code] = array();
            }
            $filterRules[$code][] = $rule;
        }

        return $filterRules;
    }

    /**
     * Returns true if the filter should be displayed
     * @param Mage_Catalog_Block_Layer_Filter_Abstract $filter
     * @param number $categoryId
     * @return bool
     */
    public function displayFilter(Mage_Catalog_Block_Layer_Filter_Abstract $filter, $categoryId)
    {
        $inclusionFilterRules = $this->getFilterRules(self::RULES_TYPE_INCLUSION);
        $exclusionFilterRules = $this->getFilterRules(self::RULES_TYPE_EXCLUSION);
        $display = true;

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        $attribute = $filter->getAttributeModel();
        if (!$attribute) {
            return $display;
        }

        $code = $attribute->getAttributeCode();

        // if there is a rule for this code, validate
        if (isset($inclusionFilterRules[$code])) {
            $display = false;
            $rules = $inclusionFilterRules[$code];
            foreach ($rules as $rule) {
                // if there is not a value for the rule, then there is nothing to validate against and it has passed
                // if there is a value, then check that they match
                $ruleCategoryId = (!$rule['category_id'] ? true : $rule['category_id'] == $categoryId);
                if ($ruleCategoryId) {
                    $display = true;
                    break;
                }
            }
        }

        if ($display == true && isset($exclusionFilterRules[$code])) {
            $rules = $exclusionFilterRules[$code];
            foreach ($rules as $rule) {
                if ($rule['category_id'] == $categoryId) {
                    $display = false;
                    break;
                }
            }
        }
        return $display;
    }

    /**
     * @return bool
     */
    public function isDisplayLimitEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PATH_DISPLAY_LIMIT_ACTIVE);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_DISPLAY_LIMIT_LIMIT);
    }

    /**
     * @return string
     */
    public function getMoreText()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_DISPLAY_LIMIT_MORE_TEXT);
    }

    /**
     * @return string
     */
    public function getLessText()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_DISPLAY_LIMIT_LESS_TEXT);
    }

    public function newLayeredFilterNum()
    {
        $this->_layeredNavNum++;
        return $this->_layeredNavNum;
    }
} 
<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use DOMDocument;
use PEAR;
use XML_Unserializer;

/**
 *
 * @package Chamilo\Core\Home\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Block extends Element
{
    const CONFIGURATION_VISIBILITY = 'visibility';
    const CONFIGURATION_CONTEXT = 'context';

    /**
     *
     * @param string[] $configurationVariables
     * @return string[]
     */
    public static function getConfigurationVariables($configurationVariables = array())
    {
        return parent :: getConfigurationVariables(
            array(self :: CONFIGURATION_VISIBILITY, self :: CONFIGURATION_CONTEXT));
    }

    /**
     *
     * @return boolean
     */
    public function getVisibility()
    {
        return $this->getSetting(self :: CONFIGURATION_VISIBILITY);
    }

    /**
     *
     * @param boolean $visibility
     */
    public function setVisibility($visibility)
    {
        $this->setSetting(self :: CONFIGURATION_VISIBILITY, $visibility);
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->getSetting(self :: CONFIGURATION_CONTEXT);
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->setSetting(self :: CONFIGURATION_CONTEXT, $context);
    }

    // /**
    // *
    // * @return boolean
    // */
    // public function create_initial_settings()
    // {
    // $context = $this->get_context();
    // $file = Path :: getInstance()->namespaceToFullPath($context) . '/Type/' . $this->get_block() . '.xml';

    // $result = array();

    // if (file_exists($file))
    // {
    // $unserializer = new XML_Unserializer();
    // $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
    // $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
    // $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
    // $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
    // $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('category', 'setting'));

    // // userialize the document
    // $status = $unserializer->unserialize($file, true);

    // if (PEAR :: isError($status))
    // {
    // throw new \Exception('Error: ' . $status->getMessage());
    // }
    // else
    // {
    // $data = $unserializer->getUnserializedData();

    // $setting_categories = $data['settings']['category'];
    // foreach ($setting_categories as $setting_category)
    // {
    // foreach ($setting_category['setting'] as $setting)
    // {
    // $block_config = new BlockConfiguration();
    // $block_config->set_block_id($this->get_id());
    // $block_config->set_variable($setting['name']);
    // $block_config->set_value($setting['default']);

    // if (! $block_config->create())
    // {
    // return false;
    // }
    // }
    // }
    // }
    // }
    // return true;
    // }

    // /**
    // *
    // * @return multitype
    // */
    // public function get_configuration()
    // {
    // $condition = new EqualityCondition(
    // new PropertyConditionVariable(BlockConfiguration :: class_name(), BlockConfiguration :: PROPERTY_BLOCK_ID),
    // new StaticConditionVariable($this->get_id()));
    // $configs = DataManager :: retrieves(BlockConfiguration :: class_name(), $condition);
    // $configuration = array();

    // while ($config = $configs->next_result())
    // {
    // $configuration[$config->get_variable()] = $config->get_value();
    // }
    // return $configuration;
    // }

    // /**
    // *
    // * @return boolean
    // */
    // public function is_configurable()
    // {
    // $context = $this->get_context();
    // $file = Path :: getInstance()->namespaceToFullPath($context) . '/Type/' . $this->get_block() . '.xml';

    // if (file_exists($file))
    // {
    // return true;
    // }
    // else
    // {
    // return false;
    // }
    // }

    // /**
    // *
    // * @return multitype
    // */
    // public function parse_settings()
    // {
    // $context = $this->get_context();
    // $component = $this->get_component();

    // $file = Path :: getInstance()->namespaceToFullPath($context) . '/Type/' . $component . '.xml';
    // $result = array();

    // if (file_exists($file))
    // {
    // $doc = new DOMDocument();
    // $doc->load($file);
    // $object = $doc->getElementsByTagname('block')->item(0);
    // $name = $object->getAttribute('name');

    // // Get categories
    // $categories = $doc->getElementsByTagname('category');
    // $settings = array();

    // foreach ($categories as $index => $category)
    // {
    // $category_name = $category->getAttribute('name');
    // $category_properties = array();

    // // Get settings in category
    // $properties = $category->getElementsByTagname('setting');
    // $attributes = array('field', 'default', 'locked');

    // foreach ($properties as $index => $property)
    // {
    // $property_info = array();

    // foreach ($attributes as $index => $attribute)
    // {
    // if ($property->hasAttribute($attribute))
    // {
    // $property_info[$attribute] = $property->getAttribute($attribute);
    // }
    // }

    // if ($property->hasChildNodes())
    // {
    // $property_options = $property->getElementsByTagname('options')->item(0);
    // $property_options_attributes = array('type', 'source');
    // foreach ($property_options_attributes as $index => $options_attribute)
    // {
    // if ($property_options->hasAttribute($options_attribute))
    // {
    // $property_info['options'][$options_attribute] = $property_options->getAttribute(
    // $options_attribute);
    // }
    // }

    // if ($property_options->getAttribute('type') == 'static' && $property_options->hasChildNodes())
    // {
    // $options = $property_options->getElementsByTagname('option');
    // $options_info = array();
    // foreach ($options as $option)
    // {
    // $options_info[$option->getAttribute('value')] = $option->getAttribute('name');
    // }
    // $property_info['options']['values'] = $options_info;
    // }
    // }
    // $category_properties[$property->getAttribute('name')] = $property_info;
    // }

    // $settings[$category_name] = $category_properties;
    // }

    // $result['name'] = $name;
    // $result['settings'] = $settings;
    // }

    // return $result;
    // }
}

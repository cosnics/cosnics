<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use DOMDocument;
use PEAR;
use XML_Unserializer;

/**
 *
 * @package home.lib
 */
class Block extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_COLUMN = 'column_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_REGISTRATION_ID = 'registration_id';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_USER = 'user_id';

    /**
     *
     * @var BlockRegistration
     */
    private $registration;

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_COLUMN,
                self :: PROPERTY_TITLE,
                self :: PROPERTY_SORT,
                self :: PROPERTY_REGISTRATION_ID,
                self :: PROPERTY_VISIBILITY,
                self :: PROPERTY_USER));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     *
     * @return int
     */
    public function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    public function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    /**
     *
     * @return int
     */
    public function get_column()
    {
        return $this->get_default_property(self :: PROPERTY_COLUMN);
    }

    public function set_column($column)
    {
        $this->set_default_property(self :: PROPERTY_COLUMN, $column);
    }

    /**
     *
     * @return int
     */
    public function get_registration_id()
    {
        return $this->get_default_property(self :: PROPERTY_REGISTRATION_ID);
    }

    public function set_registration_id($registration_id)
    {
        $this->set_default_property(self :: PROPERTY_REGISTRATION_ID, $registration_id);
    }

    /**
     *
     * @return BlockRegistration
     */
    public function get_registration()
    {
        if (! isset($this->registration))
        {
            $this->registration = DataManager :: retrieve_by_id(
                BlockRegistration :: class_name(),
                intval($this->get_registration_id()));
        }
        return $this->registration;
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     *
     * @return string
     */
    public function get_context()
    {
        return $this->get_registration()->get_context();
    }

    /**
     *
     * @deprecated Use get_block() instead
     * @return string
     */
    public function get_component()
    {
        return $this->get_block();
    }

    /**
     *
     * @return string
     */
    public function get_block()
    {
        return $this->get_registration()->get_block();
    }

    /**
     *
     * @return integer
     */
    public function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    public function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    /**
     *
     * @return integer
     */
    public function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    public function set_visibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
    }

    public function set_visible()
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, true);
    }

    public function set_invisible()
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, false);
    }

    /**
     *
     * @return boolean
     */
    public function is_visible()
    {
        return $this->get_visibility();
    }

    /*
     * (non-PHPdoc) @see common\libraries.DataClass::create()
     */
    public function create()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_COLUMN),
            new StaticConditionVariable($this->get_column()));
        $this->set_sort(DataManager :: retrieve_next_value(self :: class_name(), self :: PROPERTY_SORT, $condition));

        if (! DataManager :: create($this))
        {
            return false;
        }

        $success_settings = $this->create_initial_settings();

        return true;
    }

    public function delete()
    {
        if (! DataManager :: delete_home_block_configs($this))
        {
            return false;
        }

        return parent :: delete();
    }

    /**
     *
     * @return boolean
     */
    public function create_initial_settings()
    {
        $context = $this->get_context();
        $file = Path :: getInstance()->namespaceToFullPath($context) . '/Type/' . $this->get_block() . '.xml';

        $result = array();

        if (file_exists($file))
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('category', 'setting'));

            // userialize the document
            $status = $unserializer->unserialize($file, true);

            if (PEAR :: isError($status))
            {
                throw new \Exception('Error: ' . $status->getMessage());
            }
            else
            {
                $data = $unserializer->getUnserializedData();

                $setting_categories = $data['settings']['category'];
                foreach ($setting_categories as $setting_category)
                {
                    foreach ($setting_category['setting'] as $setting)
                    {
                        $block_config = new BlockConfiguration();
                        $block_config->set_block_id($this->get_id());
                        $block_config->set_variable($setting['name']);
                        $block_config->set_value($setting['default']);

                        if (! $block_config->create())
                        {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     *
     * @return multitype
     */
    public function get_configuration()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(BlockConfiguration :: class_name(), BlockConfiguration :: PROPERTY_BLOCK_ID),
            new StaticConditionVariable($this->get_id()));
        $configs = DataManager :: retrieves(BlockConfiguration :: class_name(), $condition);
        $configuration = array();

        while ($config = $configs->next_result())
        {
            $configuration[$config->get_variable()] = $config->get_value();
        }
        return $configuration;
    }

    /**
     *
     * @return boolean
     */
    public function is_configurable()
    {
        $context = $this->get_context();
        $file = Path :: getInstance()->namespaceToFullPath($context) . '/Type/' . $this->get_block() . '.xml';

        if (file_exists($file))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return multitype
     */
    public function parse_settings()
    {
        $context = $this->get_context();
        $component = $this->get_component();

        $file = Path :: getInstance()->namespaceToFullPath($context) . '/Type/' . $component . '.xml';
        $result = array();

        if (file_exists($file))
        {
            $doc = new DOMDocument();
            $doc->load($file);
            $object = $doc->getElementsByTagname('block')->item(0);
            $name = $object->getAttribute('name');

            // Get categories
            $categories = $doc->getElementsByTagname('category');
            $settings = array();

            foreach ($categories as $index => $category)
            {
                $category_name = $category->getAttribute('name');
                $category_properties = array();

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = array('field', 'default', 'locked');

                foreach ($properties as $index => $property)
                {
                    $property_info = array();

                    foreach ($attributes as $index => $attribute)
                    {
                        if ($property->hasAttribute($attribute))
                        {
                            $property_info[$attribute] = $property->getAttribute($attribute);
                        }
                    }

                    if ($property->hasChildNodes())
                    {
                        $property_options = $property->getElementsByTagname('options')->item(0);
                        $property_options_attributes = array('type', 'source');
                        foreach ($property_options_attributes as $index => $options_attribute)
                        {
                            if ($property_options->hasAttribute($options_attribute))
                            {
                                $property_info['options'][$options_attribute] = $property_options->getAttribute(
                                    $options_attribute);
                            }
                        }

                        if ($property_options->getAttribute('type') == 'static' && $property_options->hasChildNodes())
                        {
                            $options = $property_options->getElementsByTagname('option');
                            $options_info = array();
                            foreach ($options as $option)
                            {
                                $options_info[$option->getAttribute('value')] = $option->getAttribute('name');
                            }
                            $property_info['options']['values'] = $options_info;
                        }
                    }
                    $category_properties[$property->getAttribute('name')] = $property_info;
                }

                $settings[$category_name] = $category_properties;
            }

            $result['name'] = $name;
            $result['settings'] = $settings;
        }

        return $result;
    }

    /**
     *
     * @see \libraries\storage\DisplayOrderDataClassListenerSupport::get_display_order_property()
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_SORT);
    }

    /**
     *
     * @see \libraries\storage\DisplayOrderDataClassListenerSupport::get_display_order_context_properties()
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_COLUMN));
    }
}

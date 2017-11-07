<?php
namespace Chamilo\Core\Repository\Instance\Form;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PersonalInstance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PlatformInstance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
 *
 * @package repository.lib.external_instance_manager
 */
class InstanceForm extends FormValidator
{
    const SETTINGS_PREFIX = 'settings';

    private $external_instance;

    private $configuration;

    private $application;

    public function __construct(Application $application, $external_instance = null)
    {
        parent::__construct(
            'external_instance',
            'post',
            $application->get_url(
                array(
                    Manager::PARAM_IMPLEMENTATION => $application->get_implementation(),
                    Manager::PARAM_INSTANCE_ID => Request::get(Manager::PARAM_INSTANCE_ID))));

        $this->application = $application;
        $this->external_instance = $external_instance;
        $this->configuration = $this->parse_settings();

        if ($external_instance instanceof Instance)
        {
            $this->build_editing_form();
        }
        else
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $external_instance = $this->external_instance;
        $configuration = $this->configuration;

        $tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);
        $tabs_generator->add_tab(
            new DynamicFormTab(
                'general',
                'General',
                Theme::getInstance()->getCommonImagePath('Place/Tab/View'),
                'build_general_form'));

        if (count($configuration['settings']) > 0)
        {
            $tabs_generator->add_tab(
                new DynamicFormTab(
                    'settings',
                    'Settings',
                    Theme::getInstance()->getCommonImagePath('Place/Tab/Settings'),
                    'build_settings_form'));
        }

        $tabs_generator->render();
    }

    public function build_general_form()
    {
        $this->addElement(
            'static',
            null,
            Translation::get('ExternalInstanceType', null, Manager::get_namespace()),
            Translation::get('TypeName', null, $this->application->get_implementation()));
        $this->addElement('hidden', Instance::PROPERTY_IMPLEMENTATION, $this->application->get_implementation());
        $this->addElement(
            'text',
            Instance::PROPERTY_TITLE,
            Translation::get('Title', null, Manager::get_namespace()),
            array("size" => "50"));
        $this->addRule(
            Instance::PROPERTY_TITLE,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement(
            'checkbox',
            Instance::PROPERTY_ENABLED,
            Translation::get('Enabled', null, Utilities::COMMON_LIBRARIES));

        if ($this->application->get_user()->is_platform_admin())
        {
            if ($this->external_instance instanceof Instance)
            {
                $this->addElement(
                    'static',
                    null,
                    Translation::get('Type'),
                    Translation::get(
                        ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                            $this->external_instance->get_type())));
            }
            else
            {
                $this->addElement(
                    'radio',
                    Instance::PROPERTY_TYPE,
                    Translation::get('Type'),
                    Translation::get(PlatformInstance::class_name(false)),
                    PlatformInstance::class_name());
                $this->addElement(
                    'radio',
                    Instance::PROPERTY_TYPE,
                    null,
                    Translation::get(PersonalInstance::class_name(false)),
                    PersonalInstance::class_name());
            }
        }
        else
        {
            $this->addElement('hidden', Instance::PROPERTY_TYPE, PersonalInstance::class_name());
        }
    }

    public function build_settings_form()
    {
        $external_instance = $this->external_instance;
        $configuration = $this->configuration;

        $categories = count($configuration['settings']);

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            $has_settings = false;

            foreach ($settings as $name => $setting)
            {
                $label = Translation::get(
                    (string) StringUtilities::getInstance()->createString($name)->upperCamelize(),
                    null,
                    $this->application->get_implementation());
                $name = self::SETTINGS_PREFIX . '[' . $name . ']';
                if (! $has_settings && $categories > 1)
                {
                    $this->addElement(
                        'category',
                        Translation::get(
                            (string) StringUtilities::getInstance()->createString($category_name)->upperCamelize(),
                            null,
                            $this->application->get_implementation()));
                    $has_settings = true;
                }

                if ($setting['locked'] == 'true')
                {
                    $this->addElement('static', $name, $label, $setting['default']);
                }
                elseif ($setting['field'] == 'text')
                {
                    $this->add_textfield($name, $label, ($setting['required'] == 'true'));

                    $validations = $setting['validations'];
                    if ($validations)
                    {
                        foreach ($validations as $validation)
                        {
                            if ($this->is_valid_validation_method($validation['rule']))
                            {
                                if ($validation['rule'] != 'regex')
                                {
                                    $validation['format'] = NULL;
                                }

                                $this->addRule(
                                    $name,
                                    Translation::get(
                                        $validation['message'],
                                        null,
                                        $this->application->get_implementation()),
                                    $validation['rule'],
                                    $validation['format']);
                            }
                        }
                    }
                }
                elseif ($setting['field'] == 'html_editor')
                {
                    $this->add_html_editor($name, $label, ($setting['required'] == 'true'));
                }
                elseif ($setting['field'] == 'password')
                {
                    $this->add_password($name, $label, ($setting['required'] == 'true'));
                }
                else
                {
                    $options_type = $setting['options']['type'];
                    if ($options_type == 'dynamic')
                    {
                        $options_source = $setting['options']['source'];
                        $class = $this->application->get_implementation() . '\SettingsConnector';
                        $options = call_user_func(array($class, $options_source));
                    }
                    else
                    {
                        $options = $setting['options']['values'];
                    }

                    if ($setting['field'] == 'radio' || $setting['field'] == 'checkbox')
                    {
                        $group = array();
                        foreach ($options as $option_value => $option_name)
                        {
                            if ($setting['field'] == 'checkbox')
                            {
                                $group[] = & $this->createElement($setting['field'], $name, null, null, $option_value);
                            }
                            else
                            {
                                $group[] = & $this->createElement(
                                    $setting['field'],
                                    $name,
                                    null,
                                    Translation::get(
                                        (string) StringUtilities::getInstance()->createString($option_name)->upperCamelize(),
                                        null,
                                        $this->application->get_implementation()),
                                    $option_value);
                            }
                        }
                        $this->addGroup($group, $name, $label, '', false);
                    }
                    elseif ($setting['field'] == 'select')
                    {
                        $this->addElement('select', $name, $label, $options);
                    }
                }
            }

            if ($has_settings && $categories > 1)
            {
                $this->addElement('category');
            }
        }
    }

    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', Instance::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_external_instance()
    {
        $external_instance = $this->external_instance;
        $values = $this->exportValues();

        $external_instance->set_title($values[Instance::PROPERTY_TITLE]);
        $external_instance->set_implementation($values[Instance::PROPERTY_IMPLEMENTATION]);
        $external_instance->set_creation_date(time());
        $external_instance->set_modification_date(time());

        if (isset($values[Instance::PROPERTY_ENABLED]))
        {
            $external_instance->set_enabled(true);
        }
        else
        {
            $external_instance->set_enabled(false);
        }

        if (! $external_instance->update())
        {
            return false;
        }
        else
        {
            $settings = $values['settings'];
            $failures = 0;

            foreach ($settings as $name => $value)
            {
                $setting = DataManager::retrieve_setting_from_variable_name($name, $external_instance->get_id());
                $setting->set_value($value);

                if (! $setting->update())
                {
                    $failures ++;
                }
            }

            if ($failures > 0)
            {
                return false;
            }
        }

        return true;
    }

    public function create_external_instance()
    {
        $values = $this->exportValues();

        $type = $values[Instance::PROPERTY_TYPE];

        $external_instance = new $type();
        $external_instance->set_title($values[Instance::PROPERTY_TITLE]);
        $external_instance->set_implementation($values[Instance::PROPERTY_IMPLEMENTATION]);
        $external_instance->set_creation_date(time());
        $external_instance->set_modification_date(time());

        if (isset($values[Instance::PROPERTY_ENABLED]))
        {
            $external_instance->set_enabled(true);
        }
        else
        {
            $external_instance->set_enabled(false);
        }

        if ($external_instance instanceof PersonalInstance)
        {
            $external_instance->set_user_id($this->application->get_user_id());
        }

        if (! $external_instance->create())
        {
            return false;
        }
        else
        {
            $settings = $values['settings'];
            $failures = 0;

            foreach ($settings as $name => $value)
            {
                $setting = DataManager::retrieve_setting_from_variable_name($name, $external_instance->get_id());
                $setting->set_value($value);

                if (! $setting->update())
                {
                    $failures ++;
                }
            }

            if ($failures > 0)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $external_instance = $this->external_instance;

        if ($external_instance instanceof Instance)
        {
            $defaults[Instance::PROPERTY_ID] = $external_instance->get_id();
            $defaults[Instance::PROPERTY_TITLE] = $external_instance->get_title();
            $defaults[Instance::PROPERTY_IMPLEMENTATION] = $external_instance->get_implementation();
            $defaults[Instance::PROPERTY_ENABLED] = $external_instance->get_enabled();
            $defaults[Instance::PROPERTY_TYPE] = $external_instance->get_type();

            $configuration = $this->configuration;

            foreach ($configuration['settings'] as $category_name => $settings)
            {
                foreach ($settings as $name => $setting)
                {
                    $setting = DataManager::retrieve_setting_from_variable_name($name, $external_instance->get_id());
                    if ($setting instanceof Setting)
                    {
                        $defaults[self::SETTINGS_PREFIX][$name] = $setting->get_value();
                    }
                }
            }
        }
        else
        {
            $defaults[Instance::PROPERTY_TYPE] = PersonalInstance::class_name();
        }

        parent::setDefaults($defaults);
    }

    public function parse_settings()
    {
        $file = Path::getInstance()->namespaceToFullPath($this->application->get_implementation()) . 'Resources' .
             DIRECTORY_SEPARATOR . 'Settings' . DIRECTORY_SEPARATOR . 'Settings.xml';

        $result = array();

        if (file_exists($file))
        {
            $doc = new DOMDocument();
            $doc->load($file);
            $object = $doc->getElementsByTagname('package')->item(0);
            $name = $object->getAttribute('context');

            // Get categories
            $categories = $doc->getElementsByTagname('category');
            $settings = array();

            foreach ($categories as $index => $category)
            {
                $category_name = $category->getAttribute('name');
                $category_properties = array();

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = array('field', 'default', 'locked', 'user_setting');

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

                        if ($property_options)
                        {
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

                        $property_validations = $property->getElementsByTagname('validations')->item(0);

                        if ($property_validations)
                        {
                            if ($property_validations->hasChildNodes())
                            {
                                $validations = $property_validations->getElementsByTagname('validation');
                                $validation_info = array();
                                foreach ($validations as $validation)
                                {
                                    $validation_info[] = array(
                                        'rule' => $validation->getAttribute('rule'),
                                        'message' => $validation->getAttribute('message'),
                                        'format' => $validation->getAttribute('format'));
                                }
                                $property_info['validations'] = $validation_info;
                            }
                        }
                    }
                    $category_properties[$property->getAttribute('name')] = $property_info;
                }

                $settings[$category_name] = $category_properties;
            }

            $result['context'] = $name;
            $result['settings'] = $settings;
        }

        return $result;
    }

    private function is_valid_validation_method($validation_method)
    {
        $available_validation_methods = array('regex', 'email', 'lettersonly', 'alphanumeric', 'numeric', 'required');
        return in_array($validation_method, $available_validation_methods);
    }
}

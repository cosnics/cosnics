<?php
namespace Chamilo\Core\Admin\Form;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\Admin\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
 * $Id: configuration_form.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 *
 * @package admin.lib
 * @author Hans De Bisschop
 */

/**
 * A form to configure platform settings.
 */
class ConfigurationForm extends FormValidator
{

    private $context;

    private $configuration;

    private $is_user_setting_form;

    /**
     * Constructor.
     *
     * @param $application string The name of the application.
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     */
    public function __construct($context, $form_name, $method = 'post', $action = null, $is_user_setting_form = false)
    {
        parent :: __construct($form_name, $method, $action);

        $this->is_user_setting_form = $is_user_setting_form;
        $this->context = $context;
        // TODO: It might be better to move this functionality to the Path-class

        $this->configuration = $this->parse_application_settings();

        $this->build_form();
        $this->setDefaults();
    }

    /**
     *
     * @param $setting array
     */
    public function setting_is_available(array $setting)
    {
        $connector_class = $this->context . '\SettingsConnector';

        $is_user_setting = isset($setting['user_setting']) && $setting['user_setting'] == 1;
        $has_availability_method = isset($setting['availability']) && isset($setting['availability']['source']) &&
             StringUtilities :: getInstance()->hasValue($setting['availability']['source']);

        if ($this->is_user_setting_form)
        {
            if ($is_user_setting)
            {
                if ($has_availability_method)
                {
                    $availability_method_exists = ($has_availability_method ? method_exists(
                        $connector_class,
                        $setting['availability']['source']) : false);
                    if ($availability_method_exists)
                    {
                        return call_user_func(array($connector_class, $setting['availability']['source']));
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return true;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Builds a form to create or edit a learning object.
     * Creates fields for default learning object properties. The
     * result of this function is equal to build_creation_form()'s, but that one may be overridden to extend the form.
     */
    private function build_form()
    {
        $context = $this->context;
        $configuration = $this->configuration;

        if (count($configuration['settings']) > 0)
        {
            $connector_class = $context . '\SettingsConnector';

            foreach ($configuration['settings'] as $category_name => $settings)
            {
                $has_settings = false;

                foreach ($settings as $name => $setting)
                {
                    if (! $this->setting_is_available($setting))
                    {
                        continue;
                    }

                    if (! $has_settings)
                    {
                        $this->addElement('html', '<div class="configuration_form">');
                        $this->addElement(
                            'html',
                            '<span class="category">' . Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($category_name)->upperCamelize(),
                                null,
                                $context) . '</span>');
                        $has_settings = true;
                    }

                    if ($setting['locked'] == 'true')
                    {
                        $this->addElement(
                            'static',
                            $name,
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($name)->upperCamelize(),
                                null,
                                $context));
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield(
                            $name,
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($name)->upperCamelize(),
                                null,
                                $context),
                            ($setting['required'] == 'true'));

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
                                        Translation :: get($validation['message'], null, $context),
                                        $validation['rule'],
                                        $validation['format']);
                                }
                            }
                        }
                    }
                    elseif ($setting['field'] == 'html_editor')
                    {
                        $this->add_html_editor(
                            $name,
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($name)->upperCamelize(),
                                null,
                                $context),
                            ($setting['required'] == 'true'));
                    }
                    elseif ($setting['field'] == 'password')
                    {
                        $this->add_password(
                            $name,
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($name)->upperCamelize(),
                                null,
                                $context),
                            ($setting['required'] == 'true'));
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $options = call_user_func(array($connector_class, $options_source));
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
                                    $group[] = & $this->createElement(
                                        $setting['field'],
                                        $name,
                                        null,
                                        null,
                                        $option_value);
                                }
                                else
                                {
                                    $group[] = & $this->createElement(
                                        $setting['field'],
                                        $name,
                                        null,
                                        Translation :: get(
                                            (string) StringUtilities :: getInstance()->createString($option_name)->upperCamelize(),
                                            null,
                                            $context),
                                        $option_value);
                                }
                            }
                            $this->addGroup(
                                $group,
                                $name,
                                Translation :: get(
                                    (string) StringUtilities :: getInstance()->createString($name)->upperCamelize(),
                                    null,
                                    $context),
                                '<br/>',
                                false);
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement(
                                'select',
                                $name,
                                Translation :: get(
                                    (string) StringUtilities :: getInstance()->createString($name)->upperCamelize(),
                                    null,
                                    $context),
                                $options);
                        }
                    }
                }

                if ($has_settings)
                {
                    $this->addElement('html', '<div style="clear: both;"></div>');
                    $this->addElement('html', '</div>');
                }
            }

            $buttons = array();
            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation :: get('Save', array(), Utilities :: COMMON_LIBRARIES),
                array('class' => 'positive'));
            $buttons[] = $this->createElement(
                'style_reset_button',
                'reset',
                Translation :: get('Reset', array(), Utilities :: COMMON_LIBRARIES),
                array('class' => 'normal empty'));
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement(
                'html',
                '<div class="warning-message">' .
                     Translation :: get('NoConfigurableSettings', array(), Utilities :: COMMON_LIBRARIES) . '</div>');
        }
    }

    public function parse_application_settings()
    {
        $context = $this->context;

        $file = Path :: getInstance()->namespaceToFullPath($context) . 'Resources/Settings/settings.xml';
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

                        $property_availability = $property->getElementsByTagname('availability')->item(0);

                        if ($property_availability)
                        {
                            $property_availability_attributes = array('source');

                            foreach ($property_availability_attributes as $index => $availability_attribute)
                            {
                                if ($property_availability->hasAttribute($availability_attribute))
                                {
                                    $property_info['availability'][$availability_attribute] = $property_availability->getAttribute(
                                        $availability_attribute);
                                }
                            }
                        }
                    }
                    $category_properties[$property->getAttribute('name')] = $property_info;
                }

                $settings[$category_name] = $category_properties;
            }

            $result['context'] = $context;
            $result['settings'] = $settings;
        }

        return $result;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $configuration = $this->configuration;

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['user_setting'] && $this->is_user_setting_form)
                {
                    $configuration_value = LocalSetting :: get($name, $this->context);
                }
                else
                {
                    $configuration_value = PlatformSetting :: get($name, $this->context);
                }

                if (isset($configuration_value))
                {
                    $defaults[$name] = $configuration_value;
                }
                else
                {
                    $defaults[$name] = $setting['default'];
                }
            }
        }

        parent :: setDefaults($defaults);
    }

    /**
     * Updates the configuration.
     *
     * @return boolean True if the update succeeded, false otherwise.
     */
    public function update_configuration()
    {
        $values = $this->exportValues();
        $configuration = $this->configuration;
        $context = $this->context;
        $problems = 0;

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['locked'] != 'true')
                {
                    $platform_setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
                        $name,
                        $context);

                    if (! $platform_setting instanceof Setting)
                    {
                        $platform_setting = new Setting();
                        $platform_setting->set_context($context);
                        $platform_setting->set_variable($name);
                        $platform_setting->set_user_setting($setting['locked'] ? 1 : 0);

                        if (isset($values[$name]))
                        {
                            $platform_setting->set_value($values[$name]);
                        }
                        else
                        {
                            $platform_setting->set_value(0);
                        }
                        if (! $platform_setting->create())
                        {
                            $problems ++;
                        }
                    }
                    else
                    {

                        if (isset($values[$name]))
                        {
                            $platform_setting->set_value($values[$name]);
                        }
                        else
                        {
                            $platform_setting->set_value(0);
                        }
                        if (! $platform_setting->update())
                        {
                            $problems ++;
                        }
                    }
                }
            }
        }

        if ($problems > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function update_user_settings()
    {
        $values = $this->exportValues();
        $adm = DataManager :: get_instance();
        $problems = 0;

        foreach ($this->configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if (! $this->setting_is_available($setting))
                {
                    continue;
                }

                if ($setting['locked'] != 'true' && $setting['user_setting'])
                {
                    if (isset($values[$name]))
                    {
                        $value = $values[$name];
                    }
                    else
                    {
                        $value = 0;
                    }

                    $setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
                        $name,
                        $this->context);

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(UserSetting :: class_name(), UserSetting :: PROPERTY_SETTING_ID),
                        new StaticConditionVariable($setting->get_id()));
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(UserSetting :: class_name(), UserSetting :: PROPERTY_USER_ID),
                        new StaticConditionVariable(Session :: get_user_id()));
                    $condition = new AndCondition($conditions);
                    $user_setting = \Chamilo\Core\User\Storage\DataManager :: retrieve(
                        \Chamilo\Core\User\Storage\DataClass\UserSetting :: class_name(),
                        new DataClassRetrieveParameters($condition));

                    if ($user_setting)
                    {
                        $user_setting->set_value($value);
                        if (! $user_setting->update())
                        {
                            $problems ++;
                        }
                    }
                    else
                    {
                        $user_setting = new UserSetting();
                        $user_setting->set_setting_id($setting->get_id());
                        $user_setting->set_value($value);
                        $user_setting->set_user_id(Session :: get_user_id());
                        if (! $user_setting->create())
                        {
                            $problems ++;
                        }
                    }
                }
            }
        }

        if ($problems > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    private function is_valid_validation_method($validation_method)
    {
        $available_validation_methods = array('regex', 'email', 'lettersonly', 'alphanumeric', 'numeric');
        return in_array($validation_method, $available_validation_methods);
    }
}

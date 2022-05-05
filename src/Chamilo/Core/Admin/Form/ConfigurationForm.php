<?php
namespace Chamilo\Core\Admin\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\Admin\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
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
     * @param $method string The method to use (self::FORM_METHOD_POST or self::FORM_METHOD_GET).
     * @param $action string The URL to which the form should be submitted.
     */
    public function __construct($context, $form_name, $method = self::FORM_METHOD_POST, $action = null, $is_user_setting_form = false)
    {
        parent::__construct($form_name, $method, $action);

        $this->is_user_setting_form = $is_user_setting_form;
        $this->context = $context;
        // TODO: It might be better to move this functionality to the Path-class

        $this->configuration = $this->parse_application_settings();

        $this->build_form();
        $this->setDefaults();
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

        if (is_array($configuration['settings']) && count($configuration['settings']) > 0)
        {
            $connector_class = $context . '\SettingsConnector';

            foreach ($configuration['settings'] as $category_name => $settings)
            {
                $has_settings = false;

                foreach ($settings as $name => $setting)
                {
                    if (!$this->setting_is_available($setting))
                    {
                        continue;
                    }

                    if (!$has_settings)
                    {
                        $this->addElement('html', '<fieldset>');
                        $this->addElement(
                            'html', '<legend>' . Translation::get(
                                (string) StringUtilities::getInstance()->createString($category_name)->upperCamelize(),
                                null, $context
                            ) . '</legend>'
                        );
                        $has_settings = true;
                    }

                    if ($this->isLocked($setting))
                    {
                        $this->addElement(
                            'static', $name, Translation::get(
                            (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                            $context
                        )
                        );
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield(
                            $name, Translation::get(
                            (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                            $context
                        ), ($setting['required'] == 'true')
                        );

                        $validations = $setting['validations'];
                        if ($validations)
                        {
                            foreach ($validations as $validation)
                            {
                                if ($this->is_valid_validation_method($validation['rule']))
                                {
                                    if ($validation['rule'] != 'regex')
                                    {
                                        $validation['format'] = null;
                                    }

                                    $this->addRule(
                                        $name, Translation::get($validation['message'], null, $context),
                                        $validation['rule'], $validation['format']
                                    );
                                }
                            }
                        }
                    }
                    elseif ($setting['field'] == 'html_editor')
                    {
                        $this->add_html_editor(
                            $name, Translation::get(
                            (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                            $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'image_uploader')
                    {
                        $this->addImageUploader(
                            $name, Translation::get(
                            (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                            $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'password')
                    {
                        $this->add_password(
                            $name, Translation::get(
                            (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                            $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $settingsConnector = new $connector_class();
                            $options = $settingsConnector->$options_source();
                        }
                        else
                        {
                            $options = $setting['options']['values'];
                        }

                        if ($setting['field'] == 'radio' || $setting['field'] == 'checkbox' ||
                            $setting['field'] == 'toggle')
                        {
                            $group = [];

                            foreach ($options as $option_value => $option_name)
                            {
                                if ($setting['field'] == 'checkbox' || $setting['field'] == 'toggle')
                                {
                                    $group[] = &$this->createElement(
                                        $setting['field'], $name, null, null, $option_value
                                    );
                                }
                                else
                                {
                                    $group[] = &$this->createElement(
                                        $setting['field'], $name, null, Translation::get(
                                        (string) StringUtilities::getInstance()->createString($option_name)
                                            ->upperCamelize(), null, $context
                                    ), $option_value
                                    );
                                }
                            }

                            $this->addGroup(
                                $group, $name, Translation::get(
                                (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                                $context
                            ), '', false
                            );
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement(
                                'select', $name, Translation::get(
                                (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), null,
                                $context
                            ), $options, array('class' => 'form-control')
                            );
                        }
                    }
                }

                if ($has_settings)
                {
                    $this->addElement('html', '</fieldset>');
                }
            }

            $buttons = [];
            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', Translation::get('Save', [], Utilities::COMMON_LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button', 'reset', Translation::get('Reset', [], Utilities::COMMON_LIBRARIES)
            );
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement(
                'html', '<div class="warning-message">' .
                Translation::get('NoConfigurableSettings', [], Utilities::COMMON_LIBRARIES) . '</div>'
            );
        }
    }

    protected function isHidden($setting)
    {
        return isset($setting['hidden']) && ($setting['hidden'] == 1 || $setting['hidden'] == 'true');
    }

    protected function isLocked($setting)
    {
        return isset($setting['locked']) && ($setting['locked'] == 1 || $setting['locked'] == 'true');
    }

    protected function isUserSetting($setting)
    {
        return isset($setting['user_setting']) && ($setting['user_setting'] == 1 || $setting['user_setting'] == 'true');
    }

    private function is_valid_validation_method($validation_method)
    {
        $available_validation_methods = array('regex', 'email', 'lettersonly', 'alphanumeric', 'numeric');

        return in_array($validation_method, $available_validation_methods);
    }

    public function parse_application_settings()
    {
        $context = $this->context;

        $file = Path::getInstance()->namespaceToFullPath($context) . 'Resources/Settings/settings.xml';
        $result = [];

        if (file_exists($file))
        {
            $doc = new DOMDocument();
            $doc->load($file);
            $object = $doc->getElementsByTagname('package')->item(0);
            $name = $object->getAttribute('context');

            // Get categories
            $categories = $doc->getElementsByTagname('category');
            $settings = [];

            foreach ($categories as $index => $category)
            {
                $category_name = $category->getAttribute('name');
                $category_properties = [];

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = array('field', 'default', 'locked', 'user_setting', 'hidden');

                foreach ($properties as $index => $property)
                {
                    $property_info = [];

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
                                        $options_attribute
                                    );
                                }
                            }

                            if ($property_options->getAttribute('type') == 'static' &&
                                $property_options->hasChildNodes())
                            {
                                $options = $property_options->getElementsByTagname('option');
                                $options_info = [];
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
                                $validation_info = [];
                                foreach ($validations as $validation)
                                {
                                    $validation_info[] = array(
                                        'rule' => $validation->getAttribute('rule'),
                                        'message' => $validation->getAttribute('message'),
                                        'format' => $validation->getAttribute('format')
                                    );
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
                                    $property_info['availability'][$availability_attribute] =
                                        $property_availability->getAttribute(
                                            $availability_attribute
                                        );
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
    public function setDefaults($defaults = [], $filter = null)
    {
        $configuration = $this->configuration;

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['user_setting'] && $this->is_user_setting_form)
                {
                    $configuration_value = LocalSetting::getInstance()->get($name, $this->context);
                }
                else
                {
                    $configuration_value = Configuration::getInstance()->get_setting(array($this->context, $name));
                }

                if (isset($configuration_value) && ($configuration_value == 0 || !empty($configuration_value)))
                {
                    $defaults[$name] = $configuration_value;
                }
                else
                {
                    $defaults[$name] = $setting['default'];
                }
            }
        }

        parent::setDefaults($defaults);
    }

    /**
     *
     * @param $setting array
     */
    public function setting_is_available(array $setting)
    {
        $connector_class = $this->context . '\SettingsConnector';

        $is_user_setting = $this->isUserSetting($setting);
        $isHidden = $this->isHidden($setting);

        $has_availability_method = isset($setting['availability']) && isset($setting['availability']['source']) &&
            StringUtilities::getInstance()->hasValue($setting['availability']['source']);

        if ($this->is_user_setting_form)
        {
            if ($is_user_setting && !$isHidden)
            {
                if ($has_availability_method)
                {
                    $availability_method_exists = ($has_availability_method && method_exists(
                            $connector_class, $setting['availability']['source']
                        ));
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
            return !$isHidden;
        }
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
                if (!$this->isLocked($setting) && !$this->isHidden($setting))
                {
                    $platform_setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
                        $name, $context
                    );

                    if (!$platform_setting instanceof Setting)
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
                        if (!$platform_setting->create())
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
                        if (!$platform_setting->update())
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
        $adm = DataManager::getInstance();
        $problems = 0;

        foreach ($this->configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if (!$this->setting_is_available($setting))
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

                    $setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
                        $name, $this->context
                    );

                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
                        new StaticConditionVariable($setting->get_id())
                    );
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
                        new StaticConditionVariable(Session::get_user_id())
                    );
                    $condition = new AndCondition($conditions);
                    $user_setting = \Chamilo\Core\User\Storage\DataManager::retrieve(
                        UserSetting::class, new DataClassRetrieveParameters($condition)
                    );

                    if ($user_setting)
                    {
                        $user_setting->set_value($value);
                        if (!$user_setting->update())
                        {
                            $problems ++;
                        }
                    }
                    else
                    {
                        $user_setting = new UserSetting();
                        $user_setting->set_setting_id($setting->get_id());
                        $user_setting->set_value($value);
                        $user_setting->set_user_id(Session::get_user_id());
                        if (!$user_setting->create())
                        {
                            $problems ++;
                        }
                    }
                }
            }
        }

        LocalSetting::getInstance()->resetCache();

        if ($problems > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}

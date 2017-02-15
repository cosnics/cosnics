<?php
namespace Chamilo\Core\Repository\External\Action\Form;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
 * A form to configure external repository settings.
 *
 * @package application.common
 * @author Hans De Bisschop
 */
class SettingsForm extends FormValidator
{

    private $external_repository;

    private $configuration;

    private $configurer;
    const TAB_ADMIN = 'admin';

    /**
     * Constructor.
     *
     * @param $application string The name of the application.
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     */
    public function __construct($configurer, $external_repository_id, $form_name, $method = 'post', $action = null)
    {
        parent::__construct($form_name, $method, $action);

        $this->configurer = $configurer;
        $this->external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_by_id(
            Instance::class_name(),
            $external_repository_id
        );
        $this->configuration = $this->parse_settings();
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
        $this->tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);
        $has_settings = $this->external_repository->has_settings();
        $is_platform = $this->configurer->get_user()->is_platform_admin();

        if ($has_settings)
        {
            $tab_admin = new DynamicFormTab(
                self::TAB_ADMIN,
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_ADMIN)->upperCamelize() . 'Settings'
                ),
                null,
                'build_configure_form'
            );

            $this->tabs_generator->add_tab($tab_admin);
        }

        $this->tabs_generator->render();
    }

    public function build_configure_form()
    {
        $external_repository = $this->external_repository;
        $configuration = $this->configuration;

        if (count($configuration['settings']) > 0)
        {
            $categories = count($configuration['settings']);

            foreach ($configuration['settings'] as $category_name => $settings)
            {
                $has_settings = false;

                foreach ($settings as $name => $setting)
                {

                    if (!$has_settings && $categories > 1)
                    {
                        $this->addElement('html', '<div class="configuration_form">');
                        $this->addElement(
                            'html',
                            '<span class="category">' .
                            Translation::get(
                                (string) StringUtilities::getInstance()->createString($category_name)->upperCamelize(),
                                null,
                                $configuration['context']
                            ) .
                            '</span>'
                        );
                        $has_settings = true;
                    }

                    if ($setting['locked'] == 'true')
                    {
                        $this->addElement(
                            'static',
                            $name,
                            Translation::get(
                                (string) StringUtilities::getInstance()->createString($name)->upperCamelize()
                            ),
                            null,
                            $configuration['context']
                        );
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield(
                            $name,
                            Translation::get(
                                (string) StringUtilities::getInstance()->createString($name)->upperCamelize(),
                                null,
                                $configuration['context']
                            ),
                            ($setting['required'] == 'true')
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
                                        $name,
                                        Translation::get(
                                            $validation['message'],
                                            null,
                                            \Chamilo\Core\Repository\External\Manager::get_namespace(
                                                $configuration['context']
                                            )
                                        ),
                                        $validation['rule'],
                                        $validation['format']
                                    );
                                }
                            }
                        }
                    }
                    elseif ($setting['field'] == 'html_editor')
                    {
                        $this->add_html_editor(
                            $name,
                            Translation::get(
                                (string) StringUtilities::getInstance()->createString($name)->upperCamelize(),
                                $configuration['context']
                            ),
                            ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'password')
                    {
                        $this->addElement(
                            'password',
                            $name,
                            Translation::get(
                                (string) StringUtilities::getInstance()->createString($name)->upperCamelize(),
                                $configuration['context']
                            )
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
                                        $name,
                                        Translation::get(
                                            $validation['message'],
                                            null,
                                            \Chamilo\Core\Repository\External\Manager::get_namespace(
                                                $configuration['context']
                                            )
                                        ),
                                        $validation['rule'],
                                        $validation['format']
                                    );
                                }
                            }
                        }
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $class = $configuration['context'] . '\SettingsConnector';
                            $options = call_user_func(array($class, $options_source));
                        }
                        else
                        {
                            $options = $setting['options']['values'];
                        }

                        if ($setting['field'] == 'radio' || $setting['field'] == 'checkbox' ||
                            $setting['field'] == 'toggle'
                        )
                        {
                            $group = array();
                            foreach ($options as $option_value => $option_name)
                            {
                                if ($setting['field'] == 'checkbox' || $setting['field'] == 'toggle')
                                {
                                    $group[] = &$this->createElement(
                                        $setting['field'],
                                        $name,
                                        null,
                                        null,
                                        $option_value
                                    );
                                }
                                else
                                {
                                    $group[] = &$this->createElement(
                                        $setting['field'],
                                        $name,
                                        null,
                                        Translation::get(
                                            (string) StringUtilities::getInstance()->createString($option_name)
                                                ->upperCamelize()
                                        ),
                                        $option_value
                                    );
                                }
                            }
                            $this->addGroup(
                                $group,
                                $name,
                                Translation::get(
                                    (string) StringUtilities::getInstance()->createString($name)->upperCamelize(),
                                    null,
                                    $configuration['context']
                                ),
                                '',
                                false
                            );
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement(
                                'select',
                                $name,
                                Translation::get(
                                    (string) StringUtilities::getInstance()->createString($name)->upperCamelize(),
                                    null,
                                    $configuration['context']
                                ),
                                $options
                            );
                        }
                    }
                }

                if ($has_settings && $categories > 1)
                {
                    $this->addElement('html', '<div style="clear: both;"></div>');
                    $this->addElement('html', '</div>');
                }
            }

            $buttons = array();
            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation::get('Save', null, Utilities::COMMON_LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button',
                'reset',
                Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
            );
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement('html', Translation::get('NoConfigurableSettings', null, Utilities::COMMON_LIBRARIES));
        }
    }

    public function parse_settings()
    {
        $external_repository = $this->external_repository;

        $file = Path::getInstance()->namespaceToFullPath($external_repository->get_implementation()) .
            'Resources/Settings/Settings.xml';
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
                                        $options_attribute
                                    );
                                }
                            }

                            if ($property_options->getAttribute('type') == 'static' &&
                                $property_options->hasChildNodes()
                            )
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
                                        'format' => $validation->getAttribute('format')
                                    );
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

    /**
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $has_settings = $this->external_repository->has_settings();
        $is_platform = $this->configurer->get_user()->is_platform_admin();
        $external_repository = $this->external_repository;
        $configuration = $this->configuration;

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($has_settings)
                {
                    $admin_value = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting::get(
                        $name,
                        $this->configurer->get_external_repository()->get_id()
                    );
                    $defaults[$name] = (isset($admin_value) ? $admin_value : $setting['default']);
                }
            }
        }

        parent::setDefaults($defaults);
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
        $external_repository = $this->external_repository;
        $is_platform = $this->configurer->get_user()->is_platform_admin();
        $problems = 0;

        foreach ($configuration['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['locked'] != 'true')
                {
                    $setting =
                        \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_setting_from_variable_name(
                            $name,
                            $external_repository->get_id()
                        );
                    if ($setting instanceof \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting)
                    {
                        if (isset($values[$name]))
                        {
                            $setting->set_value($values[$name]);
                        }
                        else
                        {
                            $setting->set_value(0);
                        }
                        if (!$setting->update())
                        {
                            $problems ++;
                        }
                    }
                    else
                    {
                        $setting = new \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting();
                        $setting->set_external_id($external_repository->get_id());
                        $setting->set_variable($name);

                        if (isset($values[$name]))
                        {
                            $setting->set_value($values[$name]);
                        }
                        else
                        {
                            $setting->set_value(0);
                        }

                        if (!$setting->create())
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
        $available_validation_methods = array('regex', 'email', 'lettersonly', 'alphanumeric', 'numeric', 'required');

        return in_array($validation_method, $available_validation_methods);
    }
}

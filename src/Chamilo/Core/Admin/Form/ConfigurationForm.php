<?php
namespace Chamilo\Core\Admin\Form;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\StringUtilities;
use DOMDocument;

/**
 * @package admin.lib
 * @author  Hans De Bisschop
 */

/**
 * A form to configure platform settings.
 */
class ConfigurationForm extends FormValidator
{

    private array $configuration;

    private string $context;

    private bool $is_user_setting_form;

    /**
     * @throws \QuickformException
     */
    public function __construct(
        string $context, string $form_name, string $method = self::FORM_METHOD_POST, ?string $action = null,
        bool $is_user_setting_form = false
    )
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
     *
     * @throws \QuickformException
     */
    private function build_form(): void
    {
        $context = $this->context;
        $configuration = $this->configuration;

        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();

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
                            'html', '<legend>' . $translator->trans(
                                (string) $stringUtilities->createString($category_name)->upperCamelize(), [], $context
                            ) . '</legend>'
                        );
                        $has_settings = true;
                    }

                    if ($this->isLocked($setting))
                    {
                        $this->addElement(
                            'static', $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        )
                        );
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
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
                                        $name, $translator->trans($validation['message'], [], $context),
                                        $validation['rule'], $validation['format']
                                    );
                                }
                            }
                        }
                    }
                    elseif ($setting['field'] == 'html_editor')
                    {
                        $this->add_html_editor(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'image_uploader')
                    {
                        $this->addImageUploader(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        )
                        );
                    }
                    elseif ($setting['field'] == 'password')
                    {
                        $this->add_password(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
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
                                    $group[] = $this->createElement(
                                        $setting['field'], $name, null, null, $option_value
                                    );
                                }
                                else
                                {
                                    $group[] = $this->createElement(
                                        $setting['field'], $name, null, $translator->trans(
                                        (string) $stringUtilities->createString($option_name)->upperCamelize(), [],
                                        $context
                                    ), $option_value
                                    );
                                }
                            }

                            $this->addGroup(
                                $group, $name, $translator->trans(
                                (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                            ), '', false
                            );
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement(
                                'select', $name, $translator->trans(
                                (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                            ), $options, ['class' => 'form-control']
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
                'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
            );
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement(
                'html', '<div class="warning-message">' .
                $translator->trans('NoConfigurableSettings', [], StringUtilities::LIBRARIES) . '</div>'
            );
        }
    }

    public function getConfigurationService(): ConfigurationService
    {
        return $this->getService(ConfigurationService::class);
    }

    protected function getUser(): ?User
    {
        return $this->getService(User::class);
    }

    protected function isHidden($setting): bool
    {
        return isset($setting['hidden']) && ($setting['hidden'] == 1 || $setting['hidden'] == 'true');
    }

    protected function isLocked($setting): bool
    {
        return isset($setting['locked']) && ($setting['locked'] == 1 || $setting['locked'] == 'true');
    }

    protected function isUserSetting($setting): bool
    {
        return isset($setting['user_setting']) && ($setting['user_setting'] == 1 || $setting['user_setting'] == 'true');
    }

    private function is_valid_validation_method($validation_method): bool
    {
        $available_validation_methods = ['regex', 'email', 'lettersonly', 'alphanumeric', 'numeric'];

        return in_array($validation_method, $available_validation_methods);
    }

    public function parse_application_settings(): array
    {
        $context = $this->context;

        $file = $this->getSystemPathBuilder()->namespaceToFullPath($context) . 'Resources/Settings/settings.xml';
        $result = [];

        if (file_exists($file))
        {
            $doc = new DOMDocument();
            $doc->load($file);

            // Get categories
            $categories = $doc->getElementsByTagName('category');
            $settings = [];

            foreach ($categories as $category)
            {
                $category_name = $category->getAttribute('name');
                $category_properties = [];

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = ['field', 'default', 'locked', 'user_setting', 'hidden'];

                foreach ($properties as $property)
                {
                    $property_info = [];

                    foreach ($attributes as $attribute)
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
                            $property_options_attributes = ['type', 'source'];

                            foreach ($property_options_attributes as $options_attribute)
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
                                    $validation_info[] = [
                                        'rule' => $validation->getAttribute('rule'),
                                        'message' => $validation->getAttribute('message'),
                                        'format' => $validation->getAttribute('format')
                                    ];
                                }
                                $property_info['validations'] = $validation_info;
                            }
                        }

                        $property_availability = $property->getElementsByTagname('availability')->item(0);

                        if ($property_availability)
                        {
                            $property_availability_attributes = ['source'];

                            foreach ($property_availability_attributes as $availability_attribute)
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
     * @param $defaultValues array Default values for this form's parameters.
     *
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $configuration = $this->configuration;

        foreach ($configuration['settings'] as $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['user_setting'] && $this->is_user_setting_form)
                {
                    $configuration_value =
                        $this->getUserSettingService()->getSettingForUser($this->getUser(), $this->context, $name);
                }
                else
                {
                    $configuration_value = $this->getConfigurationConsulter()->getSetting([$this->context, $name]);
                }

                if (isset($configuration_value) && ($configuration_value == 0 || !empty($configuration_value)))
                {
                    $defaultValues[$name] = $configuration_value;
                }
                else
                {
                    $defaultValues[$name] = $setting['default'];
                }
            }
        }

        parent::setDefaults($defaultValues);
    }

    public function setting_is_available(array $setting)
    {
        $connector_class = $this->context . '\SettingsConnector';

        $is_user_setting = $this->isUserSetting($setting);
        $isHidden = $this->isHidden($setting);

        $has_availability_method = isset($setting['availability']) && isset($setting['availability']['source']) &&
            $this->getStringUtilities()->hasValue($setting['availability']['source']);

        if ($this->is_user_setting_form)
        {
            if ($is_user_setting && !$isHidden)
            {
                if ($has_availability_method)
                {
                    $availability_method_exists = method_exists(
                        $connector_class, $setting['availability']['source']
                    );
                    if ($availability_method_exists)
                    {
                        return call_user_func([$connector_class, $setting['availability']['source']]);
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
     * @return bool True if the update succeeded, false otherwise.
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \QuickformException
     */
    public function update_configuration(): bool
    {
        $values = $this->exportValues();

        $configurationService = $this->getConfigurationService();
        $configuration = $this->configuration;
        $context = $this->context;
        $problems = 0;

        foreach ($configuration['settings'] as $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if (!$this->isLocked($setting) && !$this->isHidden($setting))
                {
                    $platformSetting = $configurationService->findSettingByContextAndVariableName($context, $name);

                    if (!$platformSetting instanceof Setting)
                    {
                        if (!$configurationService->createSettingFromParameters(
                            $context, $name, $values[$name] ?: 0, (bool) $setting['locked']
                        ))
                        {
                            $problems ++;
                        }
                    }
                    else
                    {
                        $platformSetting->set_value($values[$name] ?: 0);

                        if (!$configurationService->updateSetting($platformSetting))
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

    /**
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function update_user_settings(): bool
    {
        $values = $this->exportValues();
        $problems = 0;

        foreach ($this->configuration['settings'] as $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if (!$this->setting_is_available($setting))
                {
                    continue;
                }

                if ($setting['locked'] != 'true' && $setting['user_setting'])
                {
                    if (!$this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                        $this->context, $name, $this->getUser(), $values[$name] ?? 0
                    ))
                    {
                        $problems ++;
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
}

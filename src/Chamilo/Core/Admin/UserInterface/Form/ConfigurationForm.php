<?php
namespace Chamilo\Core\Admin\UserInterface\Form;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorCollection;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\Admin\Service\ConfigurationService;
use Chamilo\Core\Admin\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_bootstrap_radio;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_extended_checkbox;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_toggle;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\StringUtilities;
use DOMDocument;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_select;
use HTML_QuickForm_static;
use HTML_QuickForm_text;

/**
 * @package Chamilo\Core\Admin\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationForm extends FormValidator
{

    private array $configuration;

    private string $context;

    private ?User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(
        string $context, string $formName, string $method = self::FORM_METHOD_POST, ?string $action = null,
        ?User $user = null
    )
    {
        parent::__construct($formName, $method, $action);

        $this->user = $user;
        $this->context = $context;
        $this->configuration = $this->parseSettings();

        $this->build_form();
        $this->setDefaults();
    }

    /**
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
            $settingsConnector = $this->getSettingsConnectorFactory()->getSettingsConnectorForContext($context);

            foreach ($configuration['settings'] as $category_name => $settings)
            {
                $has_settings = false;

                foreach ($settings as $name => $setting)
                {
                    if (!$this->settingIsAvailable($setting))
                    {
                        continue;
                    }

                    if (!$has_settings)
                    {
                        $this->addElement(HTML_QuickForm_html::class, '<fieldset>');
                        $this->addElement(
                            HTML_QuickForm_html::class, '<legend>' . $translator->trans(
                                (string) $stringUtilities->createString($category_name)->upperCamelize(), [], $context
                            ) . '</legend>'
                        );
                        $has_settings = true;
                    }

                    if ($this->isLocked($setting))
                    {
                        $this->addElement(
                            HTML_QuickForm_static::class, $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        )
                        );
                    }
                    elseif ($setting['field'] == HTML_QuickForm_text::class)
                    {
                        $this->addTextfield(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'HTML_QuickForm_html_editor')
                    {
                        $this->addHtmlEditor(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'HTML_QuickForm_image_uploader')
                    {
                        $this->addImageUploader(
                            $name, $translator->trans(
                            (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                        )
                        );
                    }
                    elseif ($setting['field'] == HTML_QuickForm_password::class)
                    {
                        $this->addPassword(
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

                            if ($settingsConnector instanceof SettingsConnectorInterface)
                            {
                                $options = $settingsConnector->$options_source();
                            }
                            else
                            {
                                $options = [];
                            }
                        }
                        else
                        {
                            $options = $setting['options']['values'];
                        }

                        if ($setting['field'] == HTML_QuickForm_bootstrap_radio::class ||
                            $setting['field'] == HTML_QuickForm_extended_checkbox::class ||
                            $setting['field'] == HTML_QuickForm_toggle::class)
                        {
                            $group = [];

                            foreach ($options as $option_value => $option_name)
                            {
                                if ($setting['field'] == HTML_QuickForm_extended_checkbox::class ||
                                    $setting['field'] == HTML_QuickForm_toggle::class)
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
                        elseif ($setting['field'] == HTML_QuickForm_select::class)
                        {
                            $this->addElement(
                                HTML_QuickForm_select::class, $name, $translator->trans(
                                (string) $stringUtilities->createString($name)->upperCamelize(), [], $context
                            ), $options, ['class' => 'form-control']
                            );
                        }
                    }
                }

                if ($has_settings)
                {
                    $this->addElement(HTML_QuickForm_html::class, '</fieldset>');
                }
            }

            $this->addSaveResetButtons();
        }
        else
        {
            $this->addElement(
                HTML_QuickForm_html::class, '<div class="warning-message">' .
                $translator->trans('NoConfigurableSettings', [], StringUtilities::LIBRARIES) . '</div>'
            );
        }
    }

    public function getConfigurationService(): ConfigurationService
    {
        return $this->getService(ConfigurationService::class);
    }

    public function getSettingsConnectorFactory(): SettingsConnectorCollection
    {
        return $this->getService(SettingsConnectorCollection::class);
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

    public function parseSettings(): array
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
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $configuration = $this->configuration;

        foreach ($configuration['settings'] as $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['user_setting'] && $this->user instanceof User)
                {
                    $configuration_value =
                        $this->getUserSettingService()->getSettingForUser($this->user, $this->context, $name);
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

    public function settingIsAvailable(array $setting)
    {
        $settingsConnector = $this->getSettingsConnectorFactory()->getSettingsConnectorForContext($this->context);

        $is_user_setting = $this->isUserSetting($setting);
        $isHidden = $this->isHidden($setting);

        $has_availability_method = isset($setting['availability']) && isset($setting['availability']['source']) &&
            $this->getStringUtilities()->hasValue($setting['availability']['source']);

        if ($this->user instanceof User)
        {
            if ($is_user_setting && !$isHidden)
            {
                if ($has_availability_method)
                {
                    if ($settingsConnector instanceof SettingsConnectorInterface &&
                        method_exists($settingsConnector, $setting['availability']['source']))
                    {
                        $methodName = $setting['availability']['source'];

                        return $settingsConnector->$methodName();
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function updateConfiguration(): bool
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
                        $platformSetting->setValue($values[$name] ?: 0);

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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function updateUserSettings(): bool
    {
        $values = $this->exportValues();
        $problems = 0;

        foreach ($this->configuration['settings'] as $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if (!$this->settingIsAvailable($setting))
                {
                    continue;
                }

                if ($setting['locked'] != 'true' && $setting['user_setting'])
                {
                    if (!$this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                        $this->context, $name, $this->user, $values[$name] ?? 0
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

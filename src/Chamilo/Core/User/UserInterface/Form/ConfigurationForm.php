<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_radio;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_checkbox;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use DOMDocument;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_select;
use HTML_QuickForm_static;
use HTML_QuickForm_text;

/**
 * @package Chamilo\Core\User\UserInterface\Form
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

        if (is_array($configuration['settings']) && count($configuration['settings']) > 0) {
            $settingsConnector = $this->getSettingsConnectorFactory()->getSettingsConnectorForContext($context);

            foreach ($configuration['settings'] as $categoryName => $settings) {
                $hasSettings = false;

                foreach ($settings as $name => $setting) {
                    if (!$this->settingIsAvailable($setting)) {
                        continue;
                    }

                    if (!$hasSettings) {
                        $this->addElement(HTML_QuickForm_html::class, '<fieldset>');
                        $this->addElement(
                            HTML_QuickForm_html::class,
                            '<legend>' . $translator->trans($categoryName, [], $context) . '</legend>'
                        );
                        $hasSettings = true;
                    }

                    if ($this->isLocked($setting)) {
                        $this->addElement(
                            HTML_QuickForm_static::class, $name, $translator->trans(
                            $name, [], $context
                        )
                        );
                    }
                    elseif ($setting['field'] == HTML_QuickForm_text::class) {
                        $this->addTextfield(
                            $name, $translator->trans(
                            $name, [], $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'HTML_QuickForm_html_editor') {
                        $this->addHtmlEditor(
                            $name, $translator->trans(
                            $name, [], $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    elseif ($setting['field'] == 'HTML_QuickForm_image_uploader') {
                        $this->addImageUploader(
                            $name, $translator->trans(
                            $name, [], $context
                        )
                        );
                    }
                    elseif ($setting['field'] == HTML_QuickForm_password::class) {
                        $this->addPassword(
                            $name, $translator->trans(
                            $name, [], $context
                        ), ($setting['required'] == 'true')
                        );
                    }
                    else {
                        $optionsType = $setting['options']['type'];

                        if ($optionsType == 'dynamic') {
                            $optionsSource = $setting['options']['source'];

                            if ($settingsConnector instanceof SettingsConnectorInterface) {
                                $options = $settingsConnector->$optionsSource();
                            }
                            else {
                                $options = [];
                            }
                        }
                        else {
                            $options = $setting['options']['values'];
                        }

                        if ($setting['field'] == HTML_QuickForm_button_radio::class) {
                            $this->addRadioButton($name, $translator->trans($name, [], $context), $options);
                        }
                        elseif ($setting['field'] == HTML_QuickForm_checkbox::class) {
                            $this->addCheckbox($name, $translator->trans($name, [], $context));
                        }
                        elseif ($setting['field'] == HTML_QuickForm_select::class) {
                            $this->addElement(
                                HTML_QuickForm_select::class, $name, $translator->trans($name, [], $context), $options,
                                ['class' => 'form-control']
                            );
                        }
                    }
                }

                if ($hasSettings) {
                    $this->addElement(HTML_QuickForm_html::class, '</fieldset>');
                }
            }

            $this->addSaveResetButtons();
        }
        else {
            $this->addElement(
                HTML_QuickForm_html::class, '<div class="alert alert-warning">' .
                $translator->trans('NoConfigurableSettings', [], StringUtilities::LIBRARIES) . '</div>'
            );
        }
    }

    public function getSettingsConnectorFactory(): SettingsConnectorRegistry
    {
        return $this->getService(SettingsConnectorRegistry::class);
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

        if (file_exists($file)) {
            $doc = new DOMDocument();
            $doc->load($file);

            // Get categories
            $categories = $doc->getElementsByTagName('category');
            $settings = [];

            foreach ($categories as $category) {
                $categoryName = $category->getAttribute('name');
                $categoryProperties = [];

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = ['field', 'default', 'locked', 'user_setting', 'hidden'];

                foreach ($properties as $property) {
                    $propertyInfo = [];

                    foreach ($attributes as $attribute) {
                        if ($property->hasAttribute($attribute)) {
                            $propertyInfo[$attribute] = $property->getAttribute($attribute);
                        }
                    }

                    if ($property->hasChildNodes()) {
                        $propertyOptions = $property->getElementsByTagname('options')->item(0);

                        if ($propertyOptions) {
                            $propertyOptionsAttributes = ['type', 'source'];

                            foreach ($propertyOptionsAttributes as $optionsAttribute) {
                                if ($propertyOptions->hasAttribute($optionsAttribute)) {
                                    $propertyInfo['options'][$optionsAttribute] = $propertyOptions->getAttribute(
                                        $optionsAttribute
                                    );
                                }
                            }

                            if ($propertyOptions->getAttribute('type') == 'static' &&
                                $propertyOptions->hasChildNodes()) {
                                $options = $propertyOptions->getElementsByTagname('option');
                                $optionsInfo = [];
                                foreach ($options as $option) {
                                    $optionsInfo[$option->getAttribute('value')] = $option->getAttribute('name');
                                }
                                $propertyInfo['options']['values'] = $optionsInfo;
                            }
                        }

                        $propertyAvailability = $property->getElementsByTagname('availability')->item(0);

                        if ($propertyAvailability) {
                            $propertyAvailabilityAttributes = ['source'];

                            foreach ($propertyAvailabilityAttributes as $availabilityAttribute) {
                                if ($propertyAvailability->hasAttribute($availabilityAttribute)) {
                                    $propertyInfo['availability'][$availabilityAttribute] =
                                        $propertyAvailability->getAttribute(
                                            $availabilityAttribute
                                        );
                                }
                            }
                        }
                    }
                    $categoryProperties[$property->getAttribute('name')] = $propertyInfo;
                }

                $settings[$categoryName] = $categoryProperties;
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

        foreach ($configuration['settings'] as $settings) {
            foreach ($settings as $name => $setting) {
                $configurationValue = $this->getUserService()->findUserSetting($this->user, $name);

                if (isset($configurationValue) && ($configurationValue == 0 || !empty($configurationValue))) {
                    $defaultValues[$name] = $configurationValue;
                }
                else {
                    $defaultValues[$name] = $setting['default'];
                }
            }
        }

        parent::setDefaults($defaultValues);
    }

    public function settingIsAvailable(array $setting)
    {
        $settingsConnector = $this->getSettingsConnectorFactory()->getSettingsConnectorForContext($this->context);
        $isHidden = $this->isHidden($setting);

        $availabilitySource = $setting['availability']['source'];
        $hasAvailabilityMethod = $setting['availability'] && $availabilitySource;

        if ($this->user instanceof User) {
            if (!$isHidden) {
                if ($hasAvailabilityMethod) {
                    if (method_exists($settingsConnector, $availabilitySource)) {
                        return $settingsConnector->$availabilitySource();
                    }
                    else {
                        return false;
                    }
                }
                else {
                    return true;
                }
            }
            else {
                return false;
            }
        }
        else {
            return !$isHidden;
        }
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserSettings(): bool
    {
        $values = $this->exportValues();
        $problems = 0;

        foreach ($this->configuration['settings'] as $settings) {
            foreach ($settings as $name => $setting) {
                if (!$this->settingIsAvailable($setting)) {
                    continue;
                }

                if ($setting['locked'] != 'true' && $setting['user_setting']) {
                    if (!$this->getUserService()->updateUserSetting(
                        $this->user, $name, $values[$name]
                    )) {
                        $problems ++;
                    }
                }
            }
        }

        if ($problems > 0) {
            return false;
        }
        else {
            return true;
        }
    }
}

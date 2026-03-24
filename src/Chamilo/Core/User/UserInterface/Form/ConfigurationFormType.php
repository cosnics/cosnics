<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\User\Service\UserSettingsParser;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationFormType extends AbstractType
{
    protected FormButtonTypeBuilder $formButtonTypeBuilder;

    protected FormTypeBuilder $formTypeBuilder;

    protected SettingsConnectorRegistry $settingsConnectorRegistry;

    protected Translator $translator;

    protected UserSettingsParser $userSettingsParser;

    protected UserSettingsService $userSettingsService;

    public function __construct(
        FormButtonTypeBuilder $formButtonTypeBuilder, FormTypeBuilder $formTypeBuilder,
        SettingsConnectorRegistry $settingsConnectorRegistry, Translator $translator,
        UserSettingsParser $userSettingsParser, UserSettingsService $userSettingsService
    )
    {
        $this->formButtonTypeBuilder = $formButtonTypeBuilder;
        $this->formTypeBuilder = $formTypeBuilder;
        $this->settingsConnectorRegistry = $settingsConnectorRegistry;
        $this->translator = $translator;
        $this->userSettingsParser = $userSettingsParser;
        $this->userSettingsService = $userSettingsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $context = $options['context'];
        $configuration = $this->getUserSettingsParser()->determineConfigurablePackageContextSettings($context);
        $formTypeBuilder = $this->getFormTypeBuilder();

        $translator = $this->getTranslator();

        if (count($configuration) > 0) {
            $settingsConnector = $this->getSettingsConnectorRegistry()->getSettingsConnectorForContext($context);

            foreach ($configuration as $categoryName => $settings) {
                $formTypeBuilder->addCategory(
                    $builder, 'category_' . $categoryName, $translator->trans($categoryName, [], $context)
                );

                foreach ($settings as $name => $setting) {
                    if (!$this->getUserSettingsService()->isSettingAvailable($context, $setting)) {
                        continue;
                    }
                    $fieldName = str_replace('.', '-', $name);

                    if ($this->isLocked($setting)) {
                        $formTypeBuilder->addVisualContent($builder, $fieldName, $translator->trans($name, [], $context)
                        );
                    }
                    elseif ($setting['field'] == TextType::class) {
                        $formTypeBuilder->addText($builder, $fieldName, $translator->trans($name, [], $context),
                            ($setting['required'] == 'true'));
                    }
                    elseif ($setting['field'] == HtmlEditorFormType::class) {
                        $formTypeBuilder->addHtmlEditor($builder, $fieldName, $translator->trans($name, [], $context),
                            ($setting['required'] == 'true'));
                    }
                    elseif ($setting['field'] == PasswordType::class) {
                        $formTypeBuilder->addPassword($builder, $fieldName, $translator->trans($name, [], $context),
                            ($setting['required'] == 'true'));
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

                        $optionObjects = [];

                        foreach ($options as $optionKey => $optionValue) {
                            $optionObjects[] = new OptionsTreeChoice($optionKey, $optionValue);
                        }

                        if ($setting['field'] == RadioType::class) {
                            $formTypeBuilder->addRadio($builder, $fieldName, $translator->trans($name, [], $context),
                                ($setting['required'] == 'true'), $optionObjects);
                        }
                        elseif ($setting['field'] == CheckboxType::class) {
                            $formTypeBuilder->addCheckbox($builder, $fieldName, $translator->trans($name, [], $context)
                            );
                        }
                        elseif ($setting['field'] == ChoiceType::class) {
                            $formTypeBuilder->addSelect($builder, $fieldName, $translator->trans($name, [], $context),
                                ($setting['required'] == 'true'), $optionObjects);
                        }
                    }
                }
            }

            $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
        }
        else {
            $formTypeBuilder->addWarning(
                $builder, 'no_settings', $translator->trans('NoConfigurableSettings', [], StringUtilities::LIBRARIES)
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['context' => StringUtilities::LIBRARIES]);

        $resolver->setNormalizer('context', static function (Options $options, $context) {
            if (!is_string($context) || !$context) {
                throw new LogicException('The context must be a non-empty string.');
            }

            return $context;
        });
    }

    protected function getFormButtonTypeBuilder(): FormButtonTypeBuilder
    {
        return $this->formButtonTypeBuilder;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
    }

    public function getSettingsConnectorRegistry(): SettingsConnectorRegistry
    {
        return $this->settingsConnectorRegistry;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getUserSettingsParser(): UserSettingsParser
    {
        return $this->userSettingsParser;
    }

    public function getUserSettingsService(): UserSettingsService
    {
        return $this->userSettingsService;
    }

    protected function isLocked($setting): bool
    {
        return isset($setting['locked']) && ($setting['locked'] == 1 || $setting['locked'] == 'true');
    }
}
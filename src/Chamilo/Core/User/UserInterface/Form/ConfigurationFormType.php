<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\User\Service\UserSettingsParser;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
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
    public function __construct(
        protected readonly FormButtonTypeBuilder $formButtonTypeBuilder,
        protected readonly FormTypeBuilder $formTypeBuilder,
        protected readonly SettingsConnectorRegistry $settingsConnectorRegistry,
        protected readonly Translator $translator, protected readonly UserSettingsParser $userSettingsParser,
        protected readonly UserSettingsService $userSettingsService
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $context = $options['context'];
        $configuration = $this->userSettingsParser->determineConfigurablePackageContextSettings($context);
        $formTypeBuilder = $this->formTypeBuilder;

        $translator = $this->translator;

        if (count($configuration) > 0) {
            $settingsConnector = $this->settingsConnectorRegistry->getSettingsConnectorForContext($context);

            foreach ($configuration as $categoryName => $settings) {
                $builder->add(
                    $formTypeBuilder->createCategory(
                        $builder, 'category_' . $categoryName, $translator->trans($categoryName, [], $context)
                    )
                );

                foreach ($settings as $name => $setting) {
                    if (!$this->userSettingsService->isSettingAvailable($context, $setting)) {
                        continue;
                    }
                    $fieldName = str_replace('.', '-', $name);

                    if ($setting['field'] == TextType::class) {
                        $builder->add(
                            $formTypeBuilder->createText($builder, $fieldName, $translator->trans($name, [], $context),
                                ($setting['required'] == 'true'))
                        );
                    }
                    elseif ($setting['field'] == HtmlEditorFormType::class) {
                        $builder->add(
                            $formTypeBuilder->createHtmlEditor(
                                $builder, $fieldName, $translator->trans($name, [], $context),
                                ($setting['required'] == 'true')
                            )
                        );
                    }
                    elseif ($setting['field'] == PasswordType::class) {
                        $builder->add(
                            $formTypeBuilder->createPassword(
                                $builder, $fieldName, $translator->trans($name, [], $context),
                                ($setting['required'] == 'true')
                            )
                        );
                    }
                    elseif ($setting['field'] == CheckboxType::class) {
                        $builder->add(
                            $formTypeBuilder->createCheckbox(
                                $builder, $fieldName, $translator->trans($name, [], $context)
                            )
                        );
                    }
                    elseif (in_array($setting['field'], [RadioType::class, ChoiceType::class])) {
                        if ($settingsConnector instanceof SettingsConnectorInterface) {
                            $source = $setting['options']['source'];
                            $options = $settingsConnector->$source();
                        }
                        else {
                            $options = [];
                        }

                        if ($setting['field'] == RadioType::class) {
                            $builder->add(
                                $formTypeBuilder->createRadio(
                                    $builder, $fieldName, $translator->trans($name, [], $context),
                                    ($setting['required'] == 'true'), $options
                                )
                            );
                        }
                        else {
                            $builder->add(
                                $formTypeBuilder->createSelect(
                                    $builder, $fieldName, $translator->trans($name, [], $context),
                                    ($setting['required'] == 'true'), $options
                                )
                            );
                        }
                    }
                }
            }

            $this->formButtonTypeBuilder->addSaveAndResetButton($builder);
        }
        else {
            $builder->add(
                $formTypeBuilder->createMessage(
                    $builder, 'no_settings',
                    $translator->trans('NoConfigurableSettingsMessage', [], StringUtilities::LIBRARIES),
                    $translator->trans('NoConfigurableSettingsLabel', [], StringUtilities::LIBRARIES),
                    AlertEnum::WARNING
                )
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
}
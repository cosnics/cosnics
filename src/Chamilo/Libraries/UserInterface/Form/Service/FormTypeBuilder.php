<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\CategoryFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\MessageFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\PictureFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentFormType;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTypeBuilder
{
    protected const array DEFAULT_ROW_ATTRIBUTES = ['class' => 'form-floating mb-3'];

    protected ?string $requiredMarkup = null;

    public function __construct(protected readonly Translator $translator)
    {
    }

    protected function applyChoiceOptions(
        array &$options, array $choices, null|callable|string|PropertyPath $value = 'value',
        null|bool|callable|string|PropertyPath $label = 'label',
        null|array|callable|string|PropertyPath $attributes = 'attributes', null|bool|string $translationDomain = false
    ): void
    {
        $options['choices'] = $choices;
        $options['choice_value'] = $value;
        $options['choice_label'] = $label;
        $options['choice_attr'] = $attributes;
        $options['choice_translation_domain'] = $translationDomain;
    }

    protected function applyCommonOptions(
        array &$options, ?string $label = null, bool $required = false, array $constraints = [],
        bool $includeRowAttr = false
    ): void
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
            $constraints[] = new Assert\NotBlank();
        }

        $options['label'] = $label;
        $options['label_html'] = true;
        $options['required'] = $required;
        $options['constraints'] = $constraints;

        if ($includeRowAttr) {
            $options['row_attr'] = self::DEFAULT_ROW_ATTRIBUTES;
        }
    }

    public function createCategory(FormBuilderInterface $builder, string $name, string $label, array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label);

        return $builder->create($name, CategoryFormType::class, $options);
    }

    public function createCheckbox(
        FormBuilderInterface $builder, string $name, string $label, bool $required = false, bool $isSwitch = true,
        array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required);

        if ($isSwitch) {
            $options['label_attr'] = [
                'class' => 'checkbox-inline checkbox-switch',
            ];
        }

        return $builder->create($name, CheckboxType::class, $options);
    }

    public function createEmail(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints, true);

        return $builder->create($name, EmailType::class, $options);
    }

    public function createFile(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints);

        return $builder->create($name, FileType::class, $options);
    }

    public function createHtml(FormBuilderInterface $builder, string $name, string $html, array $options = []
    ): FormBuilderInterface
    {
        $options['html'] = $html;

        return $builder->create($name, HtmlFormType::class, $options);
    }

    public function createHtmlEditor(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints);

        return $builder->create($name, HtmlEditorFormType::class, $options);
    }

    public function createMessage(
        FormBuilderInterface $builder, string $name, string $label, string $message,
        AlertEnum $messageType = AlertEnum::INFO, array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label);

        $options['message'] = $message;
        $options['messageType'] = $messageType;

        return $builder->create($name, MessageFormType::class, $options);
    }

    public function createPassword(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true,
        bool $validateStrength = false, array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        if ($validateStrength) {
            $constraints[] = new Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_STRONG);
        }

        $this->applyCommonOptions($options, $label, $required, $constraints, true);

        return $builder->create($name, PasswordType::class, $options);
    }

    public function createPicture(
        FormBuilderInterface $builder, string $name, string $label, ?string $pictureUri = null,
        ?string $noPictureLabel = null, array $pictureStyles = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label);

        $options['pictureStyles'] = $pictureStyles;
        $options['pictureUri'] = $pictureUri;

        if ($noPictureLabel) {
            $options['noPictureLabel'] = $noPictureLabel;
        }

        return $builder->create($name, PictureFormType::class, $options);
    }

    public function createRadio(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = [],
        array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints);
        $this->applyChoiceOptions($options, $choices);

        $options['expanded'] = true;
        $options['multiple'] = false;

        return $builder->create($name, ChoiceType::class, $options);
    }

    public function createSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = [],
        array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints, true);
        $this->applyChoiceOptions($options, $choices);

        return $builder->create($name, ChoiceType::class, $options);
    }

    public function createText(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints, true);

        return $builder->create($name, TextType::class, $options);
    }

    public function createVisualContent(
        FormBuilderInterface $builder, string $name, string $label, array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, false, [], true);

        return $builder->create($name, VisualContentFormType::class, $options);
    }

    protected function getRequired(): string
    {
        if ($this->requiredMarkup === null) {
            $glyph = new FontAwesomeGlyph('asterisk', ['text-danger', 'fa-2xs'], null, 'fas');
            $this->requiredMarkup = '<span class="text-danger ms-1">' . $glyph->render() . '</span>';
        }

        return $this->requiredMarkup;
    }
}
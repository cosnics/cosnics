<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\CategoryFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\LayoutColumnFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\LayoutRowFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\MessageFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\PictureFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualButtonFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentFormType;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface[][][] $layout
     */
    public function addLayout(FormBuilderInterface $builder, array $layout): void
    {
        foreach ($layout as $rowName => $columns) {
            $row = $builder->create('row_' . $rowName, LayoutRowFormType::class);
            foreach ($columns as $columnName => $elements) {
                $column = $builder->create('column' . $rowName . '_' . $columnName, LayoutColumnFormType::class);

                foreach ($elements as $element) {
                    $column->add($element);
                }
                $row->add($column);
            }
            $builder->add($row);
        }
    }

    protected function applyChoiceOptions(
        array &$options, iterable $choices, null|callable|string|PropertyPath $value = 'value',
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

        if ($includeRowAttr && !array_key_exists('row_attr', $options)) {
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

    public function createDate(
        FormBuilderInterface $builder, string $name, string $label, array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, false, $constraints);

        return $builder->create($name, DateType::class, $options);
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

    public function createHidden(
        FormBuilderInterface $builder, string $name, array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions(options: $options, required: true, constraints: $constraints);

        return $builder->create($name, HiddenType::class, $options);
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
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null,
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
        FormBuilderInterface $builder, string $name, string $label, ?string $noPictureLabel = null,
        array $pictureStyles = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label);

        $options['pictureStyles'] = $pictureStyles;

        if ($noPictureLabel) {
            $options['noPictureLabel'] = $noPictureLabel;
        }

        return $builder->create($name, PictureFormType::class, $options);
    }

    public function createRadio(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, iterable $choices = [],
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
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, iterable $choices = [],
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

    public function createTextarea(
        FormBuilderInterface $builder, string $name, string $label, string $height = '150px;', bool $required = true,
        array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        $this->applyCommonOptions($options, $label, $required, $constraints, true);

        $options['attr']['style'] = ($options['attr']['style'] ?? '') . (' height: ' . $height);

        return $builder->create($name, TextareaType::class, $options);
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
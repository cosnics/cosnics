<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\CategoryFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\MessageFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentFormType;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTypeBuilder
{
    protected const array DEFAULT_ROW_ATTRIBUTES = ['class' => 'form-floating mb-3'];

    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function addCategory(FormBuilderInterface $builder, string $name, string $title, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createCategory($builder, $name, $title, $options));
    }

    public function addCheckbox(
        FormBuilderInterface $builder, string $name, $label, bool $required = false, bool $isSwitch = true,
        array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createCheckbox($builder, $name, $label, $required, $isSwitch, $options));
    }

    public function addDanger(
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createDanger($builder, $name, $message, $label, $options));
    }

    public function addHtml(FormBuilderInterface $builder, string $name, string $html, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createHtml($builder, $name, $html, $options));
    }

    public function addHtmlEditor(
        FormBuilderInterface $builder, string $name, string $label, bool $required = false, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createHtmlEditor($builder, $name, $label, $required, $constraints, $options));
    }

    public function addInformation(
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createInformation($builder, $name, $message, $label, $options));
    }

    public function addMessage(
        FormBuilderInterface $builder, string $name, string $message, AlertEnum $messageType = AlertEnum::INFO,
        ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createMessage($builder, $name, $message, $messageType, $label, $options));
    }

    public function addPassword(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true,
        bool $validateStrength = false, array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        return $builder->add(
            $this->createPassword($builder, $name, $label, $required, $validateStrength, $constraints, $options)
        );
    }

    public function addSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = [],
        array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createSelect($builder, $name, $label, $required, $choices, $constraints, $options));
    }

    public function addText(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createText($builder, $name, $label, $required, $constraints, $options));
    }

    public function addVisualContent(
        FormBuilderInterface $builder, string $name, string $label, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createVisualContent($builder, $name, $label, $options));
    }

    public function addWarning(
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createWarning($builder, $name, $message, $label, $options));
    }

    public function createCategory(FormBuilderInterface $builder, string $name, string $label, array $options = []
    ): FormBuilderInterface
    {
        $options['label'] = $label;
        $options['label_html'] = true;

        return $builder->create($name, CategoryFormType::class, $options);
    }

    public function createCheckbox(
        FormBuilderInterface $builder, string $name, $label, bool $required = false, bool $isSwitch = true,
        array $options = []
    ): FormBuilderInterface
    {
        $options['label'] = $label;
        $options['label_html'] = true;
        $options['required'] = $required;

        if ($isSwitch) {
            $options['label_attr'] = [
                'class' => 'checkbox-inline checkbox-switch',
            ];
        }

        return $builder->create($name, CheckboxType::class, $options);
    }

    public function createDanger(
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $this->createMessage($builder, $name, $message, AlertEnum::DANGER, $label, $options);
    }

    protected function createFormType(
        FormBuilderInterface $builder, string $type, string $name, string $label, bool $required = true,
        array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
            $constraints[] = new Assert\NotBlank();
        }

        $options['label'] = $label;
        $options['label_html'] = true;
        $options['required'] = $required;
        $options['row_attr'] = self::DEFAULT_ROW_ATTRIBUTES;
        $options['constraints'] = $constraints;

        return $builder->create($name, $type, $options);
    }

    public function createHtml(FormBuilderInterface $builder, string $name, string $html, array $options = []
    ): FormBuilderInterface
    {
        $options['html'] = $html;

        return $builder->create($name, HtmlFormType::class, $options);
    }

    public function createHtmlEditor(
        FormBuilderInterface $builder, string $name, $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        return $this->createFormType(
            $builder, HtmlEditorFormType::class, $name, $label, $required, $constraints, $options
        );
    }

    public function createInformation(
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $this->createMessage($builder, $name, $message, AlertEnum::INFO, $label, $options);
    }

    public function createMessage(
        FormBuilderInterface $builder, string $name, string $message, AlertEnum $messageType = AlertEnum::INFO,
        ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        $options ['message'] = $message;
        $options ['messageType'] = $messageType;

        return $this->createFormType(
            $builder, MessageFormType::class, $name, $label, false, [], $options
        );
    }

    public function createPassword(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true,
        bool $validateStrength = false, array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        if ($validateStrength) {
            $constraints[] = new Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_STRONG);
        }

        return $this->createFormType(
            $builder, PasswordType::class, $name, $label, $required, $constraints, $options
        );
    }

    public function createSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = [],
        array $constraints = [], array $options = []
    ): FormBuilderInterface
    {
        $options['choices'] = $choices;
        $options['choice_value'] = 'value';
        $options['choice_label'] = 'label';
        $options['choice_attr'] = 'attributes';
        $options['choice_translation_domain'] = false;

        return $this->createFormType(
            $builder, ChoiceType::class, $name, $label, $required, $constraints, $options
        );
    }

    public function createText(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = [],
        array $options = []
    ): FormBuilderInterface
    {
        return $this->createFormType(
            $builder, TextType::class, $name, $label, $required, $constraints, $options
        );
    }

    public function createVisualContent(
        FormBuilderInterface $builder, string $name, string $label, array $options = []
    ): FormBuilderInterface
    {
        return $this->createFormType(
            $builder, VisualContentFormType::class, $name, $label, false, [], $options
        );
    }

    public function createWarning(
        FormBuilderInterface $builder, string $name, string $message, ?string $label = null, array $options = []
    ): FormBuilderInterface
    {
        return $this->createMessage($builder, $name, $message, AlertEnum::WARNING, $label, $options);
    }

    protected function getRequired(): string
    {
        $glyph = new FontAwesomeGlyph('asterisk', ['text-danger', 'fa-2xs'], null, 'fas');

        return '<span class="text-danger ms-1">' . $glyph->render() . '</span>';
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
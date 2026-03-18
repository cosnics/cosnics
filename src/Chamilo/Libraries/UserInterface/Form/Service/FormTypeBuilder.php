<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\CategoryFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlEditorFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\HtmlFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\MessageFormType;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\VisualContentFormType;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

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

    public function addCategory(FormBuilderInterface $builder, string $name, string $title): FormBuilderInterface
    {
        return $builder->add($this->createCategory($builder, $name, $title));
    }

    public function addDanger(
        FormBuilderInterface $builder, string $name, string $message, ?string $title = null
    ): FormBuilderInterface
    {
        return $builder->add($this->createDanger($builder, $name, $message, $title));
    }

    public function addHtml(FormBuilderInterface $builder, string $name, string $html): FormBuilderInterface
    {
        return $builder->add($this->createHtml($builder, $name, $html));
    }

    public function addHtmlEditor(
        FormBuilderInterface $builder, string $name, string $label, bool $required = false, array $options = [],
        array $constraints = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createHtmlEditor($builder, $name, $label, $required, $options, $constraints));
    }

    public function addInformation(
        FormBuilderInterface $builder, string $name, string $message, ?string $title = null
    ): FormBuilderInterface
    {
        return $builder->add($this->createInformation($builder, $name, $message, $title));
    }

    public function addMessage(
        FormBuilderInterface $builder, string $name, string $message, AlertEnum $messageType = AlertEnum::INFO,
        ?string $title = null
    ): FormBuilderInterface
    {
        return $builder->add($this->createMessage($builder, $name, $message, $messageType, $title));
    }

    public function addSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = [],
        array $constraints = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createSelect($builder, $name, $label, $required, $choices, $constraints));
    }

    public function addText(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = []
    ): FormBuilderInterface
    {
        return $builder->add($this->createText($builder, $name, $label, $required, $constraints));
    }

    public function addWarning(
        FormBuilderInterface $builder, string $name, string $message, ?string $title = null
    ): FormBuilderInterface
    {
        return $builder->add($this->createWarning($builder, $name, $message, $title));
    }

    public function createCategory(FormBuilderInterface $builder, string $name, string $title): FormBuilderInterface
    {
        return $builder->create($name, CategoryFormType::class, ['label' => $title]);
    }

    public function createDanger(
        FormBuilderInterface $builder, string $name, string $message, ?string $title = null
    ): FormBuilderInterface
    {
        return $this->createMessage($builder, $name, $message, AlertEnum::DANGER, $title);
    }

    public function createHtml(FormBuilderInterface $builder, string $name, string $html): FormBuilderInterface
    {
        return $builder->create($name, HtmlFormType::class, ['html' => $html]);
    }

    public function createHtmlEditor(
        FormBuilderInterface $builder, string $name, $label, bool $required = true, array $options = [],
        array $constraints = []
    ): FormBuilderInterface
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
            $constraints[] = new NotBlank();
        }

        $options['label'] = $label;
        $options['required'] = $required;
        $options['constraints'] = $constraints;

        return $builder->create($name, HtmlEditorFormType::class, $options);
    }

    public function createInformation(
        FormBuilderInterface $builder, string $name, string $message, ?string $title = null
    ): FormBuilderInterface
    {
        return $this->createMessage($builder, $name, $message, AlertEnum::INFO, $title);
    }

    public function createMessage(
        FormBuilderInterface $builder, string $name, string $message, AlertEnum $messageType = AlertEnum::INFO,
        ?string $title = null
    ): FormBuilderInterface
    {
        return $builder->create(
            $name, MessageFormType::class, [
                'label' => $title,
                'message' => $message,
                'messageType' => $messageType
            ]
        );
    }

    public function createSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = [],
        array $constraints = []
    ): FormBuilderInterface
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
            $constraints[] = new NotBlank();
        }

        return $builder->create($name, ChoiceType::class, [
            'choices' => $choices,
            'choice_value' => 'value',
            'choice_label' => 'label',
            'choice_attr' => 'attributes',
            'label' => $label,
            'label_html' => true,
            'required' => $required,
            'choice_translation_domain' => false,
            'row_attr' => self::DEFAULT_ROW_ATTRIBUTES,
            'constraints' => $constraints
        ]);
    }

    public function createText(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $constraints = []
    ): FormBuilderInterface
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
            $constraints[] = new NotBlank();
        }

        return $builder->create($name, TextType::class, [
            'label' => $label,
            'label_html' => true,
            'required' => $required,
            'row_attr' => self::DEFAULT_ROW_ATTRIBUTES,
            'constraints' => $constraints
        ]);
    }

    public function createVisualContent(FormBuilderInterface $builder, string $name, string $label, string $content
    ): FormBuilderInterface
    {
        return $builder->create($name, VisualContentFormType::class, [
            'label' => $label,
            'label_html' => true,
            'content' => $content,
            'row_attr' => self::DEFAULT_ROW_ATTRIBUTES
        ]);
    }

    public function createWarning(
        FormBuilderInterface $builder, string $name, string $message, ?string $title = null
    ): FormBuilderInterface
    {
        return $this->createMessage($builder, $name, $message, AlertEnum::WARNING, $title);
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
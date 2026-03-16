<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait FormHelperTrait
{
    use FormButtonHelperTrait;

    public function addSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = []
    ): void
    {
        $builder->add($this->createSelect($builder, $name, $label, $required, $choices));
    }

    public function addText(FormBuilderInterface $builder, string $name, string $label, bool $required = true): void
    {
        $builder->add($this->createText($builder, $name, $label, $required));
    }

    public function createSelect(
        FormBuilderInterface $builder, string $name, string $label, bool $required = true, array $choices = []
    ): FormBuilderInterface
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
        }

        return $builder->create($name, ChoiceType::class, [
            'choices' => $choices,
            'label' => $label,
            'label_html' => true,
            'required' => $required,
            'choice_translation_domain' => false,
            'row_attr' => [
                'class' => 'form-floating mb-3'
            ]
        ]);
    }

    public function createText(FormBuilderInterface $builder, string $name, string $label, bool $required = true
    ): FormBuilderInterface
    {
        if ($required) {
            $label .= ' ' . $this->getRequired();
        }

        return $builder->create($name, TextType::class, [
            'label' => $label,
            'label_html' => true,
            'required' => $required,
            'row_attr' => [
                'class' => 'form-floating mb-3'
            ]
        ]);
    }

    protected function getRequired(): string
    {
        $glyph = new FontAwesomeGlyph('asterisk', ['text-danger', 'fa-2xs'], null, 'fas');

        return '<span class="text-danger ms-1">' . $glyph->render() . '</span>';
    }

    abstract public function getTranslator(): Translator;
}
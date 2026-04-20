<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\ButtonsFormType;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormButtonTypeBuilder
{
    public function __construct(protected Translator $translator)
    {
    }

    public function addSaveAndResetButton(FormBuilderInterface $builder): void
    {
        $saveText = $this->translator->trans('Save', [], StringUtilities::LIBRARIES);
        $saveGlyph = new FontAwesomeGlyph('check', ['me-1'], $saveText, 'fas');

        $this->addSubmitAndResetButton($builder, $saveText, $saveGlyph);
    }

    public function addSubmitAndResetButton(
        FormBuilderInterface $builder, string $submitText, ?InlineGlyph $submitGlyph, string $submitName = 'submit',
        array $classes = ['btn', 'btn-primary']
    ): void
    {
        $resetText = $this->translator->trans('Reset', [], StringUtilities::LIBRARIES);
        $resetGlyph = new FontAwesomeGlyph('trash-alt', ['me-1'], $resetText, 'fas');

        $buttons = [];

        $buttons[] = $this->createSubmitButton(
            builder: $builder, labelText: $submitText, labelGlyph: $submitGlyph, name: $submitName, classes: $classes
        );
        $buttons[] = $this->createResetButton(builder: $builder, labelText: $resetText, labelGlyph: $resetGlyph);

        $options['buttons'] = $buttons;

        $builder->add('buttons', ButtonsFormType::class, $options);
    }

    public function createButton(
        FormBuilderInterface $builder, string $name, ?string $labelText = null, ?InlineGlyph $labelGlyph = null,
        array $classes = [], string $type = ButtonType::class
    ): FormBuilderInterface
    {
        if (!$labelText && !$labelGlyph) {
            throw new InvalidConfigurationException('Either a label or a glyph must be provided for the button');
        }

        $label = [];

        if ($labelGlyph) {
            $label[] = $labelGlyph->render();
        }

        if ($labelText) {
            $label[] = $labelText;
        }

        if (empty($classes)) {
            $classes = ['btn', 'btn-outline-secondary'];
        }

        return $builder->create($name, $type, [
            'label' => implode('&nbsp;', $label),
            'label_html' => true,
            'attr' => ['class' => implode(' ', $classes)]
        ]);
    }

    public function createResetButton(
        FormBuilderInterface $builder, ?string $labelText = null, ?InlineGlyph $labelGlyph = null,
        string $name = 'reset'
    ): FormBuilderInterface
    {
        if (!$labelText) {
            $labelText = $this->translator->trans('Reset', [], StringUtilities::LIBRARIES);
        }

        if (!$labelGlyph) {
            $labelGlyph = new FontAwesomeGlyph('trash-alt', ['me-1'], $labelText, 'fas');
        }

        return $this->createButton($builder, $name, $labelText, $labelGlyph, ['btn', 'btn-secondary'], ResetType::class
        );
    }

    public function createSubmitButton(
        FormBuilderInterface $builder, ?string $labelText = null, ?InlineGlyph $labelGlyph = null,
        string $name = 'submit', array $classes = ['btn', 'btn-success']
    ): FormBuilderInterface
    {
        if (!$labelText) {
            $labelText = $this->translator->trans('Submit', [], StringUtilities::LIBRARIES);
        }

        if (!$labelGlyph) {
            $labelGlyph = new FontAwesomeGlyph('check', ['me-1'], $labelText, 'fas');
        }

        return $this->createButton($builder, $name, $labelText, $labelGlyph, $classes, SubmitType::class);
    }
}
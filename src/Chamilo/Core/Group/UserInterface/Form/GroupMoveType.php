<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMoveType extends AbstractType
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translator = $this->getTranslator();

        $builder->add(NestedSet::PROPERTY_PARENT_ID, TextType::class, [
            'label' => $translator->trans('NewLocation', [], Manager::CONTEXT),
            'required' => true,
            'attr' => [
                'placeholder' => 'Name',
            ],
            'row_attr' => [
                'class' => 'form-floating mb-3',
            ],
        ]);

        $saveText = $translator->trans('Save', [], StringUtilities::LIBRARIES);
        $saveGlyph = new FontAwesomeGlyph('check', ['me-1'], $saveText, 'fas');
        $saveLabel = $saveGlyph->render() . '&nbsp;' . $saveText;

        $builder->add('submit', SubmitType::class, [
            'label' => $saveLabel,
            'label_html' => true,
            'attr' => ['class' => 'btn btn-success'],
        ]);

        $resetText = $translator->trans('Reset', [], StringUtilities::LIBRARIES);
        $resetGlyph = new FontAwesomeGlyph('trash-alt', ['me-1'], $resetText, 'fas');
        $resetLabel = $resetGlyph->render() . '&nbsp;' . $resetText;

        $builder->add('reset', ResetType::class, [
            'label' => $resetLabel,
            'label_html' => true,
            'attr' => ['class' => 'btn btn-success'],
        ]);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
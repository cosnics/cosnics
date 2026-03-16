<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\UserInterface\Form\Architecture\FormHelperTrait;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMoveType extends AbstractType
{
    use FormHelperTrait;

    protected OptionsTreeRenderer $optionsTreeRenderer;

    protected Translator $translator;

    public function __construct(Translator $translator, OptionsTreeRenderer $optionsTreeRenderer)
    {
        $this->translator = $translator;
        $this->optionsTreeRenderer = $optionsTreeRenderer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translator = $this->getTranslator();

        $builder->add(Group::PROPERTY_NAME, TextType::class, [
            'disabled' => true,
            'label' => $translator->trans('Name', [], Manager::CONTEXT),
            'row_attr' => [
                'class' => 'form-floating mb-3'
            ]
        ]);

        $this->addSelect(
            $builder, NestedSet::PROPERTY_PARENT_ID, $translator->trans('NewLocation', [], Manager::CONTEXT), true,
            $this->getOptionsTreeRenderer()->getOptions()
        );

        $this->addSaveAndResetButton($builder);
    }

    public function getOptionsTreeRenderer(): OptionsTreeRenderer
    {
        return $this->optionsTreeRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
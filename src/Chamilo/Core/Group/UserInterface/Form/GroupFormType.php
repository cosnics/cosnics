<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupFormType extends AbstractType
{
    protected FormButtonTypeBuilder $formButtonTypeBuilder;

    protected FormTypeBuilder $formTypeBuilder;

    protected GroupService $groupService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected OptionsTreeRenderer $optionsTreeRenderer;

    protected Translator $translator;

    public function __construct(
        FormTypeBuilder $formTypeBuilder, FormButtonTypeBuilder $formButtonTypeBuilder, Translator $translator,
        OptionsTreeRenderer $optionsTreeRenderer, GroupsTreeTraverser $groupsTreeTraverser, GroupService $groupService
    )
    {
        $this->translator = $translator;
        $this->optionsTreeRenderer = $optionsTreeRenderer;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->groupService = $groupService;
        $this->formTypeBuilder = $formTypeBuilder;
        $this->formButtonTypeBuilder = $formButtonTypeBuilder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilderHelper = $this->formTypeBuilder;
        $translator = $this->getTranslator();

        $formTypeBuilderHelper->addText(
            $builder, Group::PROPERTY_NAME, $translator->trans('Name', [], Manager::CONTEXT)
        );

        $formTypeBuilderHelper->addText(
            $builder, Group::PROPERTY_CODE, $translator->trans('Code', [], Manager::CONTEXT)
        );

        $formTypeBuilderHelper->addSelect(
            $builder, NestedSet::PROPERTY_PARENT_ID, $translator->trans('NewLocation', [], Manager::CONTEXT), true,
            $this->getOptionsTreeRenderer()->getOptions()->toArray()
        );

        $formTypeBuilderHelper->addHtmlEditor(
            $builder, Group::PROPERTY_DESCRIPTION, $translator->trans('Description', [], Manager::CONTEXT)
        );

        $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
    }

    protected function determineDisabledGroupIdentifiers(array $rootDisabledGroupIdentifiers = []): array
    {
        $disabledGroupIdentifiers = [];

        foreach ($rootDisabledGroupIdentifiers as $rootDisabledGroupIdentifier) {
            try {
                $disabledGroup = $this->getGroupService()->findGroupByIdentifier($rootDisabledGroupIdentifier);
                $disabledSubgroupIdentifiers =
                    $this->getGroupsTreeTraverser()->findSubGroupIdentifiersForGroup($disabledGroup, true);

                $disabledGroupIdentifiers[] = $rootDisabledGroupIdentifier;
                $disabledGroupIdentifiers = array_merge($disabledGroupIdentifiers, $disabledSubgroupIdentifiers);
            }
            catch (StorageMethodException|StorageNoResultException) {
            }
        }

        return $disabledGroupIdentifiers;
    }

    public function getFormButtonTypeBuilder(): FormButtonTypeBuilder
    {
        return $this->formButtonTypeBuilder;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
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
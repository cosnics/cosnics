<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\Service\GroupFormDataMapper;
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
    public function __construct(
        protected readonly FormTypeBuilder $formTypeBuilder,
        protected readonly FormButtonTypeBuilder $formButtonTypeBuilder, protected readonly Translator $translator,
        protected readonly OptionsTreeRenderer $optionsTreeRenderer,
        protected readonly GroupsTreeTraverser $groupsTreeTraverser, protected readonly GroupService $groupService,
        protected readonly GroupFormDataMapper $groupFormDataMapper
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper($this->groupFormDataMapper);

        $builder->add(
            $this->formTypeBuilder->createText(
                $builder, Group::PROPERTY_NAME, $this->translator->trans('Name', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createText(
                $builder, Group::PROPERTY_CODE, $this->translator->trans('Code', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createSelect(
                $builder, NestedSet::PROPERTY_PARENT_ID, $this->translator->trans('NewLocation', [], Manager::CONTEXT),
                true, $this->optionsTreeRenderer->getOptions()->toArray()
            )
        );

        $builder->add(
            $this->formTypeBuilder->createHtmlEditor(
                $builder, Group::PROPERTY_DESCRIPTION, $this->translator->trans('Description', [], Manager::CONTEXT)
            )
        );

        $this->formButtonTypeBuilder->addSaveAndResetButton($builder);
    }

    protected function determineDisabledGroupIdentifiers(array $rootDisabledGroupIdentifiers = []): array
    {
        $disabledGroupIdentifiers = [];

        foreach ($rootDisabledGroupIdentifiers as $rootDisabledGroupIdentifier) {
            try {
                $disabledGroup = $this->groupService->findGroupByIdentifier($rootDisabledGroupIdentifier);
                $disabledSubgroupIdentifiers =
                    $this->groupsTreeTraverser->findSubGroupIdentifiersForGroup($disabledGroup, true);

                $disabledGroupIdentifiers[] = $rootDisabledGroupIdentifier;
                $disabledGroupIdentifiers = array_merge($disabledGroupIdentifiers, $disabledSubgroupIdentifiers);
            }
            catch (StorageMethodException|StorageNoResultException) {
            }
        }

        return $disabledGroupIdentifiers;
    }
}